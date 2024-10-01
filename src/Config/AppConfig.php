<?php declare(strict_types = 1);

namespace Contributte\Mate\Config;

final class AppConfig
{

	/**
	 * @param array<string> $scopes
	 */
	public function __construct(
		public string $appDir,
		public string $namespace,
		public array $scopes,
		public CraftersConfig $crafters,
	)
	{
	}

}
