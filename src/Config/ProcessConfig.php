<?php declare(strict_types = 1);

namespace Contributte\Crafter\Config;

final class ProcessConfig
{

	public function __construct(
		public string $cwd,
	)
	{
	}

}
