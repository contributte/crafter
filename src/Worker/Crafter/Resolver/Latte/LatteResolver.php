<?php declare(strict_types = 1);

namespace Contributte\Crafter\Worker\Crafter\Resolver\Latte;

use Contributte\Crafter\Config\CrafterConfig;
use Contributte\Crafter\Config\DataObjectConfig;
use Contributte\Crafter\Config\DataObjectField;
use Contributte\Crafter\Template\TemplateRenderer;
use Contributte\Crafter\Utils\IO;
use Contributte\Crafter\Worker\Crafter\CrafterContext;
use Contributte\Crafter\Worker\Crafter\CrafterResult;
use Contributte\Crafter\Worker\Crafter\Resolver\ResolverInterface;
use Nette\Utils\Arrays;
use Nette\Utils\FileSystem;
use Nette\Utils\Strings;

final class LatteResolver implements ResolverInterface
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
		// Prepare context
		$resolvedFile = $this->templateRenderer->render($crafterConfig->output->path, ['name' => $structureConfig->name]);
		$resolvedFilename = Strings::replace($resolvedFile, '#\\\#', '/');

		$context = new TemplateContext(
			helper: new HelperContext(
				structVar: sprintf('$%s', $structureConfig->name),
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

		$outputContent = $this->templateRenderer->renderLatte(
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
