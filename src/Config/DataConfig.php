<?php declare(strict_types = 1);

namespace Contributte\Crafter\Config;

final class DataConfig
{

	/**
	 * @param array<string, DataObjectConfig> $items
	 */
	public function __construct(
		public array $items,
	)
	{
	}

	public function has(string $key): bool
	{
		return isset($this->items[$key]);
	}

}
