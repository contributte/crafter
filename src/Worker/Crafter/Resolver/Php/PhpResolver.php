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
		// - Do: {$namespace}/UI/{$name}Presenter.php -> MyApp/UI/HomepagePresenter.php
		// - Do: /MyApp/UI/HomepagePresenter.php -> MyApp/UI/HomepagePresenter.php
		$resolvedFile = $this->templateRenderer->render($crafterConfig->output->path, [
			'namespace' => $crafterContext->craftingConfig->app->namespace,
			'name' => $structureConfig->name,
		]);
		$resolvedFile = ltrim($resolvedFile, '\/');

		// Resolve class
		// - Do: MyApp/UI/HomepagePresenter.php -> MyApp\UI\HomepagePresenter
		// - Do: MyApp\UI\HomepagePresenter.php -> MyApp\UI\HomepagePresenter
		// - Do: MyApp\UI\HomepagePresenter -> MyApp\UI\HomepagePresenter
		$resolvedClass = Strings::replace($resolvedFile, '#\/#', '\\');
		$resolvedClass = Strings::replace($resolvedClass, '#\.php$#', '');

		// Resolve classname
		// - Do: MyApp\UI\HomepagePresenter -> HomepagePresenter
		$resolvedClassName = Classes::getClassName($resolvedClass);

		// Resolve namespace
		// - Do: MyApp\UI\HomepagePresenter -> MyApp\UI
		$resolvedNamespace = Classes::getNamespace($resolvedClass);

		// Resolve namespace
		// - Do: MyApp\UI\HomepagePresenter -> MyApp
		$resolvedRootNamespace = Classes::getRootNamespace($resolvedNamespace);

		// Resolve filename
		// - Do1: MyApp\UI\HomepagePresenter.php -> MyApp/UI/HomepagePresenter
		// - Do1: UI\HomepagePresenter.php -> UI/HomepagePresenter
		// - Do2: \MyApp\UI\HomepagePresenter.php -> /Myapp/UI/HomepagePresenters (slash at the beginning)
		// - Do2: \MyApp\UI\HomepagePresenter.php -> UI/HomepagePresenter
		$resolvedFilename = Strings::replace($resolvedFile, '#\\\#', '/');
		$resolvedFilename = Strings::replace($resolvedFilename, '#^\/?' . $crafterContext->craftingConfig->app->namespace . '/#', '');

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
