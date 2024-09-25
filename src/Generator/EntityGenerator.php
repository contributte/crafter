<?php declare(strict_types = 1);

namespace Contributte\Mate\Generator;

use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Printer;

final class EntityGenerator
{

	public function generate(): void
	{
		$class = new ClassType('Demo');
		$printer = new Printer();

		$printer->printClass($class);
	}

}
