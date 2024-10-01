<?php declare(strict_types = 1);

namespace Contributte\Mate\Crafter\Worker;

use Contributte\Mate\Config\InputConfig;

final class WorkerContext
{

	public function __construct(
		public InputConfig $mate,
	)
	{
	}

}
