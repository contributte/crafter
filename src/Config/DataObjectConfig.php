<?php declare(strict_types = 1);

namespace Contributte\Crafter\Config;

final class DataObjectConfig
{

	/**
	 * @param DataObjectField[] $fields
	 * @param array<string, scalar> $vars
	 */
	public function __construct(
		public string $name,
		public array $fields,
		public array $vars,
	)
	{
	}

}
