<?php declare(strict_types = 1);

namespace Contributte\Crafter\Config;

final class CrafterConfig
{

	/**
	 * @param array<string> $scopes
	 * @param array<string> $vars
	 */
	public function __construct(
		public string $id,
		public string $name,
		public string|null $mode,
		public array $scopes,
		public CrafterFileConfig $input,
		public CrafterFileConfig $output,
		public array $vars,
	)
	{
	}

}
