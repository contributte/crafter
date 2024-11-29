<?php declare(strict_types = 1);

namespace Contributte\Crafter\Worker\Crafter\Resolver\Php;

final class ClassContext
{

	public function __construct(
		public string $className,
		public string $namespace,
		public string $rootNamespace,
		public string|null $extends = null,
	)
	{
	}

}
