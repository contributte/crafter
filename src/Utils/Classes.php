<?php declare(strict_types = 1);

namespace Contributte\Crafter\Utils;

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

	public static function getRootNamespace(string $fqn): string
	{
		$parts = explode('\\', $fqn);

		return array_shift($parts);
	}

}
