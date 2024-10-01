<?php

declare(strict_types = 1);

namespace App\Api\User\Create;

use App\Domain\User\Database\User;
use Nette\Schema\Expect;
use Nette\Schema\Schema;

final class CreateUserRequestBody
{

	public string $username;

	public string $email;

	public string $password;

	public Nette\Utils\DateTime $createdAt;

	public Nette\Utils\DateTime $updatedAt;

	public array $roles;

	public static function schema(): Schema
	{
		return Expect::structure([
			'username' => Expect::string()->required(),
			'email' => Expect::string()->required(),
			'password' => Expect::string()->required(),
			'createdAt' => Expect::string()->required(),
			'updatedAt' => Expect::string()->required(),
			'roles' => Expect::string()->required(),
		])->castTo(self::class);
	}

}
