<?php declare(strict_types = 1);

namespace Contributte\Crafter\Worker\Crafter;

use Contributte\Crafter\Config\CraftingConfig;

final class CrafterContext
{

	private function __construct(
		public CraftingConfig $craftingConfig,
	)
	{
	}

	/**
	 * @param array<string> $scopes
	 */
	public static function from(
		CraftingConfig $craftingConfig,
		array $scopes = []
	): self
	{
		if ($scopes !== []) {
			$craftingConfig->app->scopes = $scopes;
		}

		return new self(
			craftingConfig: $craftingConfig,
		);
	}

}
