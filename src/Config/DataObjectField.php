<?php declare(strict_types = 1);

namespace Contributte\Crafter\Config;

final class DataObjectField
{

	public function __construct(
		public string $name,
		public string $type,
		public bool $nullable = false,
	)
	{
	}

}
