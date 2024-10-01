<?php declare(strict_types = 1);

namespace Contributte\Mate\Config;

final class StructsConfig
{

	/**
	 * @param array<string, StructConfig> $items
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
