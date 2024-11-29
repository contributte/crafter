<?php declare(strict_types = 1);

namespace Contributte\Crafter\Config;

final class AppConfig
{

	/**
	 * @param array<string, scalar> $vars
	 * @param array<string> $scopes
	 * @param array<string, CrafterConfig> $crafters
	 */
	public function __construct(
		public string $version,
		public string $dir,
		public string $namespace,
		public array $vars,
		public string|null $preset,
		public string|null $template,
		public array $crafters,
		public array $scopes = [],
	)
	{
	}

}
