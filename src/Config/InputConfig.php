<?php declare(strict_types = 1);

namespace Contributte\Mate\Config;

final class InputConfig
{

	public function __construct(
		public MateConfig $mate,
		public ProcessConfig $process,
		public AppConfig $app,
		public StructsConfig $structs,
	)
	{
	}

}
