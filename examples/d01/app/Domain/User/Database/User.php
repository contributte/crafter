<?php

declare(strict_types = 1);

namespace App\Domain\User\Database;

use App\Model\Database\Entity\AbstractEntity;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Table;

#[Entity]
#[Table(name: 'user')]
class User extends App\Model\Database\Entity\AbstractEntity
{

	#[Column(type: 'string')]
	private string $username;

	#[Column(type: 'string')]
	private string $email;

	#[Column(type: 'string')]
	private string $password;

	#[Column(type: 'Nette\Utils\DateTime')]
	private Nette\Utils\DateTime $createdAt;

	#[Column(type: 'Nette\Utils\DateTime')]
	private Nette\Utils\DateTime $updatedAt;

	#[Column(type: 'array')]
	private array $roles;

	public function __construct(
		// TODO - generated code
	)
	{
		// TODO - generated code
	}

}
