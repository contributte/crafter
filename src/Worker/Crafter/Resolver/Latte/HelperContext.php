<?php declare(strict_types = 1);

namespace Contributte\Crafter\Worker\Crafter\Resolver\Latte;

final class HelperContext
{

	public function __construct(
		public string $structVar,
	)
	{
	}

}
