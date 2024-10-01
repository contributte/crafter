<?php declare(strict_types = 1);

namespace Contributte\Mate\Crafter\Template;

final class TemplateContext
{

	public function __construct(
		public ClassContext $class,
		public HelperContext $helper,
		public StructContext $struct,
	)
	{
	}

}
