<?php declare(strict_types = 1);

namespace Contributte\Mate\Config;

final class CrafterConfig
{

	/**
	 * @param array<string> $scopes
	 */
	public function __construct(
		public string $crafter,
		public string $template,
		public string $class,
		public string|null $mode = null,
		public array $scopes = [],
		public string|null $baseClass = null,
	)
	{
	}

}
