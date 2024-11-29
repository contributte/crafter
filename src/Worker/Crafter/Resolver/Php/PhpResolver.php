<?php declare(strict_types = 1);

namespace Contributte\Crafter\Worker\Crafter\Resolver\Php;

use Contributte\Crafter\Config\CrafterConfig;
use Contributte\Crafter\Config\DataObjectConfig;
use Contributte\Crafter\Config\DataObjectField;
use Contributte\Crafter\Template\TemplateRenderer;
use Contributte\Crafter\Utils\Classes;
use Contributte\Crafter\Utils\IO;
use Contributte\Crafter\Worker\Crafter\CrafterContext;
use Contributte\Crafter\Worker\Crafter\CrafterResult;
use Contributte\Crafter\Worker\Crafter\Resolver\ResolverInterface;
use Nette\Utils\Arrays;
use Nette\Utils\FileSystem;
use Nette\Utils\Strings;

final class PhpResolver implements ResolverInterface
{

	public function __construct(
		private TemplateRenderer $templateRenderer,
	)
	{
	}

	public function resolve(
		CrafterContext $crafterContext,
		CrafterConfig $crafterConfig,
		DataObjectConfig $structureConfig,
		CrafterResult $crafterResult
	): void
	{
		// Resolve class (use latte to replace placeholders)
		// E.q.: {$namespace}\UI\{$name}Presenter.php -> MyApp\UI\HomepagePresenter.php
		$resolvedFile = $this->templateRenderer->render($crafterConfig->output->path, [
			'namespace' => $crafterContext->craftingConfig->app->namespace,
			'name' => $structureConfig->name,
		]);

		// Resolve classname
		// E.q. MyApp\UI\HomepagePresenter.php -> MyApp\\UI\\HomepagePresenter
		$resolvedClassName = Strings::replace($resolvedFile, '#\.php$#', '');
		$resolvedClassName = Classes::getClassName($resolvedClassName);

		// Resolve namespace
		// E.q. MyApp\UI\HomepagePresenter -> MyApp\UI
		$resolvedNamespace = Classes::getNamespace($resolvedFile);

		// Resolve namespace
		// E.q. MyApp\UI\HomepagePresenter -> MyApp
		$resolvedRootNamespace = Classes::getRootNamespace($resolvedFile);

		// Resolve filename
		// E.q. MyApp\UI\HomepagePresenter.php -> UI/HomepagePresenter
		$resolvedFilename = Strings::replace($resolvedFile, '#\\\#', '/');
		$resolvedFilename = Strings::replace($resolvedFilename, '#^' . $crafterContext->craftingConfig->app->namespace . '/#', '');

		$context = new TemplateContext(
			class: new ClassContext(
				className: $resolvedClassName,
				namespace: $resolvedNamespace,
				rootNamespace: $resolvedRootNamespace,
			),
			helper: new HelperContext(
				structVar: sprintf('$%s', $structureConfig->name),
				entityClassName: sprintf('%s', ucfirst($structureConfig->name)),
				dtoClassName: sprintf('%sDto', ucfirst($structureConfig->name)),
			),
			struct: new StructContext(
				name: $structureConfig->name,
				fields: Arrays::map(
					$structureConfig->fields,
					fn (DataObjectField $field): StructFieldContext => new StructFieldContext(
						name: $field->name,
						type: $field->type,
						nullable: $field->nullable,
					)
				)
			)
		);

		// Craft (input & output)
		$intputFile = IO::realpath($crafterConfig->input->path);
		$outputFile = $crafterContext->craftingConfig->process->cwd
			. '/' . $crafterContext->craftingConfig->app->dir
			. '/' . $resolvedFilename;

		$outputContent = $this->templateRenderer->renderPhp(
			file: $intputFile,
			params: ['ctx' => $context]
		);

		FileSystem::write($outputFile, $outputContent);

		$crafterResult->add(
			crafter: $crafterConfig->id,
			state: 'crafted',
			input: $intputFile,
			output: $outputFile,
		);
	}

}
