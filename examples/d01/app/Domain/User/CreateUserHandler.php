<?php

declare(strict_types=1);

namespace App\Domain\User;

readonly class CreateUserHandler
{
	public function __construct(
		private \Doctrine\ORM\EntityManagerInterface $em,
	) {
	}


	public function __invoke(CreateUserCommand $command): object
	{
		$entity = new User(
			username: $command->username,
			email: $command->email,
			password: $command->password,
			createdAt: $command->createdAt,
			updatedAt: $command->updatedAt,
		);


		$this->em->persist($entity);
		$this->em->flush();


		return $entity;
	}
}
