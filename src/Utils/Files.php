<?php declare(strict_types = 1);

namespace Contributte\Crafter\Utils;

final class Files
{

	public static function extension(string $file): string
	{
		return pathinfo($file, PATHINFO_EXTENSION);
	}

	public static function hasSchema(string $file): string
	{
		return pathinfo($file, PATHINFO_EXTENSION);
	}

}
