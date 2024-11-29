<?php declare(strict_types = 1);

namespace Contributte\Crafter\Worker\Crafter\Resolver\Latte;

final class TemplateContext
{

	public function __construct(
		public HelperContext $helper,
		public StructContext $struct,
	)
	{
	}

}
