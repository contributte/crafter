<?php declare(strict_types = 1);

namespace Contributte\Mate\Config\Loader;

use Contributte\Mate\Config\AppConfig;
use Contributte\Mate\Config\CrafterConfig;
use Contributte\Mate\Config\CraftersConfig;
use Contributte\Mate\Config\InputConfig;
use Contributte\Mate\Config\MateConfig;
use Contributte\Mate\Config\ProcessConfig;
use Contributte\Mate\Config\StructConfig;
use Contributte\Mate\Config\StructFieldConfig;
use Contributte\Mate\Config\StructsConfig;
use Nette\Neon\Neon;
use Nette\Schema\Helpers as SchemaHelpers;
use Nette\Utils\Arrays;
use Nette\Utils\FileSystem;

final class ConfigLoader
{

	public function load(string $cwd, string $file): InputConfig
	{
		/** @phpstan-var ConfigShape $config */
		$config = $this->loadFile($file);

		// Merge presets
		$presets = $config['mate']['presets'] ?? ['default'];

		foreach ($presets as $preset) {
			$presetFile = $this->loadFile(__DIR__ . '/../../../resources/presets/' . $preset . '/config.neon');

			/** @phpstan-var ConfigShape $config */
			$config = SchemaHelpers::merge($presetFile, $config);
		}

		return new InputConfig(
			mate: new MateConfig(
				presets: $presets,
			),
			process: new ProcessConfig(
				cwd: $cwd,
			),
			app: new AppConfig(
				appDir: $config['app']['appDir'],
				namespace: $config['app']['namespace'],
				scopes: $config['app']['scopes'] ?? [],
				crafters: new CraftersConfig(
					items: Arrays::map(
						$config['app']['crafters'] ?? [],
						fn (array $crafter): CrafterConfig => new CrafterConfig(
							crafter: $crafter['crafter'],
							template: $crafter['template'],
							class: $crafter['class'],
							mode: $crafter['mode'] ?? null,
							scopes: $crafter['scopes'] ?? [],
							baseClass: $crafter['baseClass'] ?? null
						)
					)
				)
			),
			structs: new StructsConfig(
				items: Arrays::map(
					$config['structs'] ?? [],
					fn (array $struct, string $structKey): StructConfig => new StructConfig(
						name: $structKey,
						fields: Arrays::map(
							$struct['fields'] ?? [],
							fn (array $field, string $fieldKey): StructFieldConfig => new StructFieldConfig(
								name: $fieldKey,
								type: $field['type'],
								nullable: $field['nullable'] ?? false
							)
						)
					)
				)
			)
		);
	}

	/**
	 * @phpstan-return ConfigShape
	 */
	private function loadFile(string $file): array
	{
		// Read file
		$file = FileSystem::read($file);

		/** @phpstan-var ConfigShape $config */
		$config = Neon::decode($file);

		return $config;
	}

}
