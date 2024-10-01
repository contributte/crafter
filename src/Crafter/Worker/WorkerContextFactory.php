<?php declare(strict_types = 1);

namespace Contributte\Mate\Crafter\Worker;

use Contributte\Mate\Config\InputConfig;

final class WorkerContextFactory
{

	/** @var array<string> */
	private array $scopes = [];

	/**
	 * @param array<string> $scopes
	 */
	public function withScopes(array $scopes): self
	{
		$this->scopes = $scopes;

		return $this;
	}

	public function from(InputConfig $mateConfig): WorkerContext
	{
		if ($this->scopes !== []) {
			$mateConfig->app->scopes = $this->scopes;
		}

		return new WorkerContext(
			mate: $mateConfig,
		);
	}

}
