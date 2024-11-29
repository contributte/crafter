<?php declare(strict_types = 1);

namespace Contributte\Crafter\Config;

final class CraftingConfig
{

	public function __construct(
		public ProcessConfig $process,
		public AppConfig $app,
		public DataConfig $data,
	)
	{
	}

}
