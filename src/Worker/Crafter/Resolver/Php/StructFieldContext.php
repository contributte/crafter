<?php declare(strict_types = 1);

namespace Contributte\Crafter\Worker\Crafter\Resolver\Php;

final class StructFieldContext
{

	public function __construct(
		public string $name,
		public string $type,
		public bool $nullable = false,
	)
	{
	}

}
