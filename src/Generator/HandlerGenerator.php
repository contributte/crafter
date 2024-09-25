<?php declare(strict_types = 1);

namespace Contributte\Mate\Generator;

use Nette\PhpGenerator\Dumper;
use Nette\PhpGenerator\Literal;
use Nette\PhpGenerator\PhpFile;
use Nette\PhpGenerator\Printer;

final class HandlerGenerator
{

	/**
	 * @param array<array{type: string}> $fields
	 */
	public function generate(
		string $namespace,
		string $handlerClass,
		string $commandClass,
		string $entityClass,
		array $fields
	): string
	{
		$dumper = new Dumper();
		$file = new PhpFile();
		$file->setStrictTypes();

		$classNamespace = $file->addNamespace($namespace);

		$class = $classNamespace->addClass($classNamespace->simplifyType($handlerClass));
		$class->setReadOnly();

		$constructor = $class->addMethod('__construct');
		$paramEm = $constructor->addPromotedParameter('em');
		$paramEm->setPrivate();
		$paramEm->setType($classNamespace->simplifyType('Doctrine\ORM\EntityManagerInterface'));

		$invoke = $class->addMethod('__invoke');
		$invoke->setPublic();
		$invoke->setReturnType('object');
		$invoke->addParameter('command')
			->setType($commandClass);

		// Inner
		$arguments = [];

		foreach ($fields as $fieldName => $field) {
			$arguments[$fieldName] = new Literal('$command->' . $fieldName);
		}

		$invoke->addBody('$entity = ?;', [
			new Literal($dumper->format('new ' . $entityClass . '(...?:)', $arguments)),
		]);
		$invoke->addBody("\n");
		$invoke->addBody('$this->em->persist($entity);');
		$invoke->addBody('$this->em->flush();');
		$invoke->addBody("\n");
		$invoke->addBody('return $entity;');

		$printer = new Printer();

		return $printer->printFile($file);
	}

}
