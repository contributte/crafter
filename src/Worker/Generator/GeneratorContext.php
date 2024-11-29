<?php declare(strict_types = 1);

namespace Contributte\Crafter\Worker\Generator;

use Contributte\Crafter\Config\CraftingConfig;

final class GeneratorContext
{

	private function __construct(
		public CraftingConfig $craftingConfig,
	)
	{
	}

	public static function from(
		CraftingConfig $craftingConfig,
		string|null $dir = null,
	): self
	{
		if ($dir !== null) {
			$craftingConfig->app->dir = $dir;
		}

		return new self(
			craftingConfig: $craftingConfig,
		);
	}

}
