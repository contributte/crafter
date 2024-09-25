<?php declare(strict_types = 1);

namespace Contributte\Mate\Generator;

use Nette\PhpGenerator\PhpFile;
use Nette\PhpGenerator\Printer;

final class CommandGenerator
{

	/**
	 * @param array<array{type: string}> $fields
	 */
	public function generate(
		string $namespace,
		string $commandClass,
		array $fields
	): string
	{
		$file = new PhpFile();
		$file->setStrictTypes();

		$classNamespace = $file->addNamespace($namespace);

		$class = $classNamespace->addClass($classNamespace->simplifyType($commandClass));
		$class->setReadOnly();

		$constructor = $class->addMethod('__construct');

		foreach ($fields as $fieldName => $field) {
			$parameter = $constructor->addPromotedParameter($fieldName);
			$parameter->setPublic();
			$parameter->setType($field['type']);
			$parameter->setReadOnly();
		}

		$printer = new Printer();

		return $printer->printFile($file);
	}

}
