<?php declare(strict_types = 1);

namespace Contributte\Mate\Crafter\Worker;

use Contributte\Mate\Config\StructFieldConfig;
use Contributte\Mate\Crafter\Template\ClassContext;
use Contributte\Mate\Crafter\Template\HelperContext;
use Contributte\Mate\Crafter\Template\StructContext;
use Contributte\Mate\Crafter\Template\StructFieldContext;
use Contributte\Mate\Crafter\Template\TemplateContext;
use Contributte\Mate\Exception\LogicalException;
use Contributte\Mate\Template\TemplateRenderer;
use Contributte\Mate\Utils\Classes;
use Nette\Safe;
use Nette\Utils\Arrays;
use Nette\Utils\FileSystem;
use Nette\Utils\Strings;
use Psr\Log\LoggerInterface;

final class CrafterWorker
{

	public function __construct(
		private TemplateRenderer $templateRenderer,
	)
	{
	}

	public function execute(WorkerContext $workerContext, LoggerInterface $logger): CrafterResult
	{
		if ($workerContext->mate->app->scopes === []) {
			throw new LogicalException('No scope defined');
		}

		$result = new CrafterResult();

		foreach ($workerContext->mate->app->crafters->items as $crafterKey => $crafter) {
			// Skip crafter if not in scope
			if (array_intersect($crafter->scopes, $workerContext->mate->app->scopes) === []) {
				$result->add(
					crafter: $crafterKey,
					state: 'skipped',
					note: 'Scope mismatch',
				);

				continue;
			}

			foreach ($workerContext->mate->structs->items as $struct) {
				// Prepare context
				$resolvedClass = $this->templateRenderer->render($crafter->class, ['name' => $struct->name]);
				$resolvedClassName = Classes::getClassName($resolvedClass);
				$resolvedNamespace = Classes::getNamespace($resolvedClass);
				$resolvedFilename = Strings::replace($resolvedClass, '#\\\#', '/');

				$context = new TemplateContext(
					class: new ClassContext(
						class: $resolvedClass,
						className: $resolvedClassName,
						namespace: $resolvedNamespace,
						extends: $crafter->baseClass,
					),
					helper: new HelperContext(
						structVar: sprintf('$%s', $struct->name),
						entityClassName: sprintf('%s', ucfirst($struct->name)),
						dtoClassName: sprintf('%sDto', ucfirst($struct->name)),
					),
					struct: new StructContext(
						name: $struct->name,
						fields: Arrays::map(
							$struct->fields,
							fn (StructFieldConfig $field): StructFieldContext => new StructFieldContext(
								name: $field->name,
								type: $field->type,
								nullable: $field->nullable,
							)
						)
					)
				);

				// Craft (input & output)
				$intputFile = Safe::realpath($crafter->template);
				$outputFile = Safe::realpath($workerContext->mate->process->cwd . '/' . lcfirst($resolvedFilename) . '.php');

				$outputContent = $this->templateRenderer->renderFile(
					file: $intputFile,
					params: ['ctx' => $context]
				);

				FileSystem::write($outputFile, $outputContent);

				$result->add(
					crafter: $crafterKey,
					state: 'crafted',
					input: $intputFile,
					output: $outputFile,
				);
			}
		}

		return $result;
	}

}
