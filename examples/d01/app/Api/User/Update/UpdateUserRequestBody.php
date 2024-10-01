<?php

declare(strict_types = 1);

namespace App\Api\User\Update;

use App\Domain\User\Database\User;
use Nette\Schema\Expect;
use Nette\Schema\Schema;

final class UpdateUserRequestBody
{

	public string|null $username;

	public string|null $email;

	public string|null $password;

	public Nette\Utils\DateTime|null $createdAt;

	public Nette\Utils\DateTime|null $updatedAt;

	public array|null $roles;

	public static function schema(): Schema
	{
		return Expect::structure([
			'username' => Expect::string(),
			'email' => Expect::string(),
			'password' => Expect::string(),
			'createdAt' => Expect::string(),
			'updatedAt' => Expect::string(),
			'roles' => Expect::string(),
		])->castTo(self::class);
	}

}
