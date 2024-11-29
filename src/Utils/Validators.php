<?php declare(strict_types = 1);

namespace Contributte\Crafter\Utils;

final class Validators
{

	public static function empty(mixed $value): bool
	{
		return match (true) {
			$value === null => true,
			$value === '' => true,
			default => false,
		};
	}

}
