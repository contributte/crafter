<?php declare(strict_types = 1);

namespace Contributte\Crafter\Config\Loader;

use Contributte\Crafter\Config\AppConfig;
use Contributte\Crafter\Config\CrafterConfig;
use Contributte\Crafter\Config\CrafterFileConfig;
use Contributte\Crafter\Config\CraftingConfig;
use Contributte\Crafter\Config\DataConfig;
use Contributte\Crafter\Config\DataObjectConfig;
use Contributte\Crafter\Config\DataObjectField;
use Contributte\Crafter\Config\ProcessConfig;
use Contributte\Crafter\Exception\LogicalException;
use Contributte\Crafter\Exception\RuntimeException;
use Contributte\Crafter\Utils\Files;
use Contributte\Crafter\Utils\Validators;
use Nette\DI\Helpers as DIHelpers;
use Nette\Neon\Neon;
use Nette\Schema\Expect;
use Nette\Schema\Helpers;
use Nette\Schema\Processor;
use Nette\Utils\Arrays;
use Nette\Utils\FileSystem;
use Nette\Utils\Strings;

final class ConfigLoader
{

	private string|null $cwd = null;

	/** @var array<mixed> */
	private array $configs = [];

	/** @var array<string> */
	private array $files = [];

	public function __construct()
	{
		// No-op
	}

	public function withCwd(string $cwd): self
	{
		$this->cwd = $cwd;

		return $this;
	}

	/**
	 * @param array<mixed> $config
	 */
	public function withConfig(array $config): self
	{
		$this->configs[] = $config;

		return $this;
	}

	public function withFile(string $file): self
	{
		$this->files[] = $file;

		return $this;
	}

	public function load(): CraftingConfig
	{
		if ($this->cwd === null) {
			throw new LogicalException('Missing cwd');
		}

		// Defaults
		$defaults = [
			'version' => '1',
			'dir' => 'src',
			'namespace' => 'App',
			'preset' => 'default',
			'vars' => [],
			'crafters' => [],
			'data' => [],
		];

		// Merge current with defaults
		$current = $this->mergeConfig([], $defaults); // @phpstan-ignore-line

		// (1) Take file configs
		foreach ($this->files as $file) {
			// Get file config
			$fileConfig = $this->getFileConfig($file);

			// Merge current with file config
			$current = $this->mergeConfig($fileConfig, $current);
		}

		// (2) Take array configs
		foreach ($this->configs as $config) {
			// Merge current with user config
			$current = $this->mergeConfig($config, $current); // @phpstan-ignore-line
		}

		// Process config (presets, templates)
		$processed = $this->processConfig($current);

		// Create crafting config
		return $this->createConfig($this->cwd, $processed);
	}

	/**
	 * @phpstan-param ConfigShape $left
	 * @phpstan-param ConfigShape $right
	 * @phpstan-return ConfigShape
	 */
	public function mergeConfig(array $left, array $right): array
	{
		/** @var ConfigShape $return */
		$return = Helpers::merge($left, $right);

		return $return;
	}

	/**
	 * @phpstan-param ConfigShape $config
	 * @phpstan-return ConfigShape
	 */
	public function processConfig(array $config): array
	{
		// Process preset
		$preset = $config['preset'] ?? null;

		if ($preset !== null) {
			$presetDir = __DIR__ . '/../../../resources/presets/' . $preset;

			// Skip non-existing presets
			if (!is_dir($presetDir)) {
				throw new RuntimeException(sprintf('Preset template "%s" not found on path "%s"', $preset, $presetDir));
			}

			$presetFile = $this->getPresetConfig($presetDir . '/crafter.neon');

			// Expand variables
			/** @var array{crafter: CraftersShape} $presetConfig */
			$presetConfig = DIHelpers::expand($presetFile, ['cwd' => $presetDir], true);

			// Merge user defined crafters and template crafters
			$config['crafters'] = Arrays::mergeTree($config['crafters'] ?? [], $presetConfig['crafters'] ?? []); // @phpstan-ignore-line
		}

		// Process template
		$template = $config['template'] ?? null;

		if ($template !== null) {
			$projectDir = __DIR__ . '/../../../resources/projects/' . $template;

			// Skip non-existing presets
			if (!is_dir($projectDir)) {
				throw new RuntimeException(sprintf('Project template "%s" not found on path "%s"', $template, $projectDir));
			}

			$projectFile = $this->getProjectConfig($projectDir . '/crafter.neon');

			// Expand variables
			/** @var array{crafter: CraftersShape} $projectConfig */
			$projectConfig = DIHelpers::expand($projectFile, ['cwd' => $projectDir], true);

			// Merge user defined crafters and template crafters
			$config['crafters'] = Arrays::mergeTree($config['crafters'] ?? [], $projectConfig['crafters'] ?? []); // @phpstan-ignore-line
		}

		return $config;
	}

	/**
	 * @phpstan-param ConfigShape $config
	 */
	private function createConfig(string $cwd, array $config): CraftingConfig
	{
		return new CraftingConfig(
			process: new ProcessConfig(
				cwd: $cwd,
			),
			app: new AppConfig(
				version: $config['version'],
				dir: $config['dir'],
				namespace: $config['namespace'],
				vars: $config['vars'] ?? [],
				preset: $config['preset'] ?? null,
				template: $config['template'] ?? null,
				crafters: Arrays::map(
					$config['crafters'],
					function (array $crafter, string $crafterId): CrafterConfig {
						// Parse crafter id
						// E.q.: presenter:create@db+admin -> [presenter:create, db+admin]
						$matches = explode('@', $crafterId);

						// Parse crafter mode
						// E.q.: presenter:create -> [presenter, create]
						$matches2 = explode(':', $matches[0]);

						// Name
						// E.q.: [presenter, create] -> presenter
						$name = $matches2[0];

						// Mode
						// E.q.: presenter:create -> php
						$mode = $matches2[1] ?? null;

						// Scopes
						// E.q.: db+admin -> [db, admin]
						// E.q.: null -> [default]
						$scopes = explode('+', $matches[1] ?? 'default');

						// Input (@regex https://regex101.com/r/1zInYi/1)
						// E.q.: presenter/presenter.latte [resolver: latte, path: presenter/presenter.latte]
						// E.q.: raw://presenter/presenter.latte [resolver: raw, path: presenter/presenter.latte]
						$inputMatch = Strings::match($crafter['input'], '#^(?:(?P<schema>\w+):\/\/)?(?P<path>.+)$#') ?? throw new LogicalException(sprintf('Invalid input format "%s"', $crafter['input']));
						$input = new CrafterFileConfig(
							resolver: Validators::empty($inputMatch['schema']) ? Files::extension($crafter['input']) : $inputMatch['schema'],
							path: $inputMatch['path'],
							vars: []
						);

						// Output (@regex https://regex101.com/r/1zInYi/1)
						// E.q.: presenter/presenter.latte [resolver: latte, path: presenter/presenter.latte]
						// E.q.: raw://presenter/presenter.latte [resolver: raw, path: presenter/presenter.latte]
						$outputMatch = Strings::match($crafter['output'], '#^(?:(?P<schema>\w+):\/\/)?(?P<path>.+)$#') ?? throw new LogicalException(sprintf('Invalid output format "%s"', $crafter['output']));
						$output = new CrafterFileConfig(
							resolver: Validators::empty($outputMatch['schema']) ? Files::extension($crafter['output']) : $outputMatch['schema'],
							path: $outputMatch['path'],
							vars: []
						);

						return new CrafterConfig(
							id: $crafterId,
							name: $name,
							mode: $mode,
							scopes: $scopes,
							input: $input,
							output: $output,
							vars: $crafter['vars'] ?? []
						);
					}
				)
			),
			data: new DataConfig(
				items: Arrays::map(
					$config['data'],
					fn (array $data, string $dataKey): DataObjectConfig => new DataObjectConfig(
						name: $dataKey,
						fields: Arrays::map(
							$data['fields'],
							fn (array $field, string $fieldKey): DataObjectField => new DataObjectField(
								name: $fieldKey,
								type: $field['type'],
								nullable: $field['nullable'] ?? false
							)
						),
						vars: $data['vars'] ?? []
					)
				)
			)
		);
	}

	/**
	 * @phpstan-return ConfigShape
	 */
	private function getFileConfig(string $file): array
	{
		// Read file
		$file = FileSystem::read($file);

		/** @var mixed $config */
		$config = Neon::decode($file);

		// Validate
		$schema = Expect::structure([
			'version' => Expect::anyOf('1')->before(fn (mixed $v) => is_scalar($v) ? strval($v) : $v)->default('1'),
			'dir' => Expect::string()->required(),
			'namespace' => Expect::string()->default('App'),
			'vars' => Expect::arrayOf('string')->default([]),
			'preset' => Expect::string(),
			'template' => Expect::string(),
			'crafters' => Expect::arrayOf(
				Expect::structure([
					'input' => Expect::string()->required(),
					'output' => Expect::string()->required(),
					'vars' => Expect::arrayOf('string')->default([]),
				])->castTo('array'),
				Expect::string()
			),
			'data' => Expect::arrayOf(
				Expect::structure([
					'fields' => Expect::arrayOf(
						Expect::structure([
							'type' => Expect::string()->required(),
							'nullable' => Expect::bool()->default(false),
						])->castTo('array'),
						Expect::string()
					),
					'vars' => Expect::arrayOf('string')->default([]),
				])->castTo('array'),
				Expect::string()
			),
		])->castTo('array');

		/** @var ConfigShape $config */
		$config = (new Processor())->process($schema, $config);

		return $config;
	}

	/**
	 * @return array<mixed>
	 */
	private function getPresetConfig(string $file): array
	{
		// Read file
		$file = FileSystem::read($file);

		/** @var mixed $config */
		$config = Neon::decode($file);

		// Validate
		$schema = Expect::structure([
			'version' => Expect::anyOf('1')->before(fn (mixed $v) => is_scalar($v) ? strval($v) : $v)->default('1'),
			'crafters' => Expect::arrayOf(
				Expect::structure([
					'input' => Expect::string()->required(),
					'output' => Expect::string()->required(),
					'vars' => Expect::arrayOf('string')->default([]),
				])->castTo('array'),
				Expect::string()
			),
		])->castTo('array');

		/** @var array<mixed> $config */
		$config = (new Processor())->process($schema, $config);

		return $config;
	}

	/**
	 * @return array<mixed>
	 */
	private function getProjectConfig(string $file): array
	{
		// Read file
		$file = FileSystem::read($file);

		/** @var mixed $config */
		$config = Neon::decode($file);

		// Validate
		$schema = Expect::structure([
			'version' => Expect::anyOf('1')->before(fn (mixed $v) => is_scalar($v) ? strval($v) : $v)->default('1'),
			'crafters' => Expect::arrayOf(
				Expect::structure([
					'input' => Expect::string()->required(),
					'output' => Expect::string()->required(),
					'vars' => Expect::arrayOf('string')->default([]),
				])->castTo('array'),
				Expect::string()
			),
		])->castTo('array');

		/** @var array<mixed> $config */
		$config = (new Processor())->process($schema, $config);

		return $config;
	}

}
