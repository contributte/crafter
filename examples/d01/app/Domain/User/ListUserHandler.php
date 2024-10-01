<?php

declare(strict_types=1);

namespace App\Domain\User;

#[AsMessageHandler]
readonly class ListUserHandler
{

	public function __construct(
		private EntityManagerInterface $em,
		private EventDispatcherInterface $ed,
	) {
	}

	public function __invoke(ListUserCommand $command): object
	{
	}

}