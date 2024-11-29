<?php declare(strict_types = 1);

namespace Contributte\Crafter\Worker\Crafter\Resolver;

use Contributte\Crafter\Config\CrafterConfig;
use Contributte\Crafter\Config\DataObjectConfig;
use Contributte\Crafter\Worker\Crafter\CrafterContext;
use Contributte\Crafter\Worker\Crafter\CrafterResult;

interface ResolverInterface
{

	public function resolve(
		CrafterContext $crafterContext,
		CrafterConfig $crafterConfig,
		DataObjectConfig $structureConfig,
		CrafterResult $crafterResult
	): void;

}
