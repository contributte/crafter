<?php declare(strict_types = 1);

namespace Contributte\Mate\Config;

final class CraftersConfig
{

	/**
	 * @param array<string, CrafterConfig> $items
	 */
	public function __construct(
		public array $items,
	)
	{
	}

}
