<?php declare(strict_types = 1);

namespace Contributte\Mate\Config;

final class StructConfig
{

	/**
	 * @param StructFieldConfig[] $fields
	 */
	public function __construct(
		public string $name,
		public array $fields,
	)
	{
	}

}
