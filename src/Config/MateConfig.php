<?php declare(strict_types = 1);

namespace Contributte\Mate\Config;

final class MateConfig
{

	/**
	 * @param array<string> $presets
	 */
	public function __construct(
		public array $presets,
	)
	{
	}

}
