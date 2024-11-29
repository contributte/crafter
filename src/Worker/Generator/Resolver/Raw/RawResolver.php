<?php declare(strict_types = 1);

namespace Contributte\Crafter\Worker\Generator\Resolver\Raw;

use Contributte\Crafter\Config\CrafterConfig;
use Contributte\Crafter\Template\TemplateRenderer;
use Contributte\Crafter\Utils\IO;
use Contributte\Crafter\Worker\Generator\GeneratorContext;
use Contributte\Crafter\Worker\Generator\GeneratorResult;
use Contributte\Crafter\Worker\Generator\Resolver\ResolverInterface;
use Nette\Utils\FileSystem;
use Nette\Utils\Strings;

final class RawResolver implements ResolverInterface
{

	public function __construct(
		private TemplateRenderer $templateRenderer,
	)
	{
	}

	public function resolve(
		GeneratorContext $generatorContext,
		CrafterConfig $crafterConfig,
		GeneratorResult $generatorResult
	): void
	{
		// Prepare context
		$resolvedFile = $this->templateRenderer->render($crafterConfig->output->path);
		$resolvedFilename = Strings::replace($resolvedFile, '#\\\#', '/');

		// Craft (input & output)
		$intputFile = IO::realpath($crafterConfig->input->path);
		$outputFile = $generatorContext->craftingConfig->process->cwd
			. '/' . $generatorContext->craftingConfig->app->dir
			. '/' . $resolvedFilename;

		$outputContent = FileSystem::read($intputFile);

		FileSystem::write($outputFile, $outputContent);

		$generatorResult->add(
			crafter: $crafterConfig->id,
			state: 'crafted',
			input: $intputFile,
			output: $outputFile,
		);
	}

}
