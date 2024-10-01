<?php declare(strict_types = 1);

namespace Contributte\Mate\Utils;

final class Classes
{

	public static function getClassName(string $fqn): string
	{
		$parts = explode('\\', $fqn);

		return array_pop($parts);
	}

	public static function getNamespace(string $fqn): string
	{
		$parts = explode('\\', $fqn);
		array_pop($parts);

		return implode('\\', $parts);
	}

}
