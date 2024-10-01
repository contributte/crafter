<?php declare(strict_types = 1);

namespace Contributte\Mate\Config;

final class ProcessConfig
{

	public function __construct(
		public string $cwd,
	)
	{
	}

}
