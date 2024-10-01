<?php declare(strict_types = 1);

namespace Contributte\Mate\Crafter\Template;

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
