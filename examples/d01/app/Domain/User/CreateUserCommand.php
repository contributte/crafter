<?php

declare(strict_types=1);

namespace App\Domain\User;

readonly class CreateUserCommand
{

	public function __construct(
		public string $username;
		public string $email;
		public string $password;
		public Nette\Utils\DateTime $createdAt;
		public Nette\Utils\DateTime $updatedAt;
		public array $roles;
	) {
	}

}
