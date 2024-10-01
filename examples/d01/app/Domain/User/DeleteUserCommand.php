<?php

declare(strict_types=1);

namespace App\Domain\User;

readonly class DeleteUserCommand
{

	public function __construct(
		public string $id
	) {
	}

}
