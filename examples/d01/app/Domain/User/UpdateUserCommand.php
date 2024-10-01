<?php

declare(strict_types=1);

namespace App\Domain\User;

readonly class UpdateUserCommand
{

	public function __construct(
		public string|null $username;
		public string|null $email;
		public string|null $password;
		public Nette\Utils\DateTime|null $createdAt;
		public Nette\Utils\DateTime|null $updatedAt;
		public array|null $roles;
	) {
	}

}
