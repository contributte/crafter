<?php

declare(strict_types=1);

namespace App\Domain\User;

readonly class ListUserCommand
{

	public function __construct(
		public ListUserCommandFilter $filter,
	) {
	}

}
