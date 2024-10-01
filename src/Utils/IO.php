<?php declare(strict_types = 1);

namespace Contributte\Mate\Utils;

use Nette\Safe;
use Phar;

final class IO
{

	public static function realpath(string $file): string
	{
		return Phar::running() !== '' ? $file : Safe::realpath($file);
	}

}
