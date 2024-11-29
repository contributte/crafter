<?php declare(strict_types = 1);

namespace Contributte\Crafter\Worker\Crafter;

use Contributte\Crafter\Exception\LogicalException;
use Contributte\Crafter\Worker\Crafter\Resolver\Latte\LatteResolver;
use Contributte\Crafter\Worker\Crafter\Resolver\Php\PhpResolver;
use Psr\Log\LoggerInterface;

final class CrafterWorker
{

	public function __construct(
		private PhpResolver $phpResolver,
		private LatteResolver $latteResolver
	)
	{
	}

	public function execute(CrafterContext $workerContext, LoggerInterface $logger): CrafterResult
	{
		$result = new CrafterResult();

		foreach ($workerContext->craftingConfig->app->crafters as $crafterKey => $crafter) {
			// Skip crafter if not in scope
			if (array_intersect($crafter->scopes, $workerContext->craftingConfig->app->scopes) === []) {
				$result->add(
					crafter: $crafterKey,
					state: 'skipped',
					note: 'Scope mismatch',
				);

				continue;
			}

			foreach ($workerContext->craftingConfig->data->items as $structure) {
				match ($crafter->output->resolver) {
					'php' => $this->phpResolver->resolve($workerContext, $crafter, $structure, $result),
					'latte' => $this->latteResolver->resolve($workerContext, $crafter, $structure, $result),
					default => throw new LogicalException(sprintf('Unknown template resolver "%s"', $crafter->output->resolver)),
				};
			}
		}

		return $result;
	}

}
