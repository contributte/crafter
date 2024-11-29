<?php declare(strict_types = 1);

namespace Contributte\Crafter\Worker\Crafter\Resolver\Php;

final class HelperContext
{

	public function __construct(
		public string $structVar,
		public string $entityClassName,
		public string $dtoClassName
	)
	{
	}

}
