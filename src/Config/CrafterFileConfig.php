<?php declare(strict_types = 1);

namespace Contributte\Crafter\Config;

final class CrafterFileConfig
{

	/**
	 * @param array<string> $vars
	 */
	public function __construct(
		public string $resolver,
		public string $path,
		public array $vars,
	)
	{
	}

}
