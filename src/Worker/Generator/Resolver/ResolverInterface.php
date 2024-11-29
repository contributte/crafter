<?php declare(strict_types = 1);

namespace Contributte\Crafter\Worker\Generator\Resolver;

use Contributte\Crafter\Config\CrafterConfig;
use Contributte\Crafter\Worker\Generator\GeneratorContext;
use Contributte\Crafter\Worker\Generator\GeneratorResult;

interface ResolverInterface
{

	public function resolve(
		GeneratorContext $generatorContext,
		CrafterConfig $crafterConfig,
		GeneratorResult $generatorResult
	): void;

}
