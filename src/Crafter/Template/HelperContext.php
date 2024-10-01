<?php declare(strict_types = 1);

namespace Contributte\Mate\Crafter\Template;

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
