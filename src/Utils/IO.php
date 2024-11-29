<?php declare(strict_types = 1);

namespace Contributte\Crafter\Utils;

use Contributte\Crafter\Exception\RuntimeException;
use Nette\Safe;
use Phar;

final class IO
{

	public static function realpath(string $file): string
	{
		if (!file_exists($file)) {
			throw new RuntimeException(sprintf('File "%s" does not exist', $file));
		}

		return Phar::running() !== '' ? $file : Safe::realpath($file);
	}

}
