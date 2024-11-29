<?php declare(strict_types = 1);

namespace Contributte\Crafter\Worker\Crafter\Resolver\Latte;

final class StructContext
{

	/**
	 * @param StructFieldContext[] $fields
	 */
	public function __construct(
		public string $name,
		public array $fields,
	)
	{
	}

}
