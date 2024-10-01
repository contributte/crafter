<?php declare(strict_types = 1);

namespace Contributte\Mate\Crafter\Template;

final class ClassContext
{

	public function __construct(
		public string $class,
		public string $className,
		public string $namespace,
		public string|null $extends = null,
	)
	{
	}

}
