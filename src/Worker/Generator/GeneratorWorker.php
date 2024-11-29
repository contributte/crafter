<?php declare(strict_types = 1);

namespace Contributte\Crafter\Worker\Generator;

use Contributte\Crafter\Exception\LogicalException;
use Contributte\Crafter\Worker\Generator\Resolver\Raw\RawResolver;
use Psr\Log\LoggerInterface;

final class GeneratorWorker
{

	public function __construct(
		private RawResolver $rawResolver,
	)
	{
	}

	public function execute(GeneratorContext $generatorContext, LoggerInterface $logger): GeneratorResult
	{
		$result = new GeneratorResult();

		foreach ($generatorContext->craftingConfig->app->crafters as $crafter) {
			match ($crafter->output->resolver) {
				'raw' => $this->rawResolver->resolve($generatorContext, $crafter, $result),
				default => throw new LogicalException(sprintf('Unknown template resolver "%s"', $crafter->output->resolver)),
			};
		}

		return $result;
	}

}
