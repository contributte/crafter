<?php

declare(strict_types=1);

namespace App\Domain\User;

use App\Model\Bus\Result\Result;
use App\Model\Exception\Runtime\EntityNotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class UpdateUserHandler
{

	public function __construct(
		private EntityManagerInterface $em,
		private EventDispatcherInterface $ed,
	) {
	}

	public function __invoke(UpdateUserCommand $command): Result
	{
		$user = $this->em->getRepository(User::class)->findOneBy(['id' => $command->id]);

		if ($user === null) {
			throw EntityNotFoundException::notFoundByUiid(User::class, $command->id);
		}

		if ($command->username !== null) {
		$user->username = $command->username;
		}
		if ($command->email !== null) {
		$user->email = $command->email;
		}
		if ($command->password !== null) {
		$user->password = $command->password;
		}
		if ($command->createdAt !== null) {
		$user->createdAt = $command->createdAt;
		}
		if ($command->updatedAt !== null) {
		$user->updatedAt = $command->updatedAt;
		}
		if ($command->roles !== null) {
		$user->roles = $command->roles;
		}

		$this->em->persist($user);
		$this->em->flush();

		// $this->ed->dispatch(new UserUpdatedEvent($user));

		return Result::from($user);
	}

}
