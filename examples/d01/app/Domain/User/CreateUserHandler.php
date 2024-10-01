<?php

declare(strict_types=1);

namespace App\Domain\User;

use App\Model\Bus\Result\Result;
use App\Model\Exception\Runtime\EntityExistsException;
use App\Model\Exception\Runtime\EntityNotFoundException;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class CreateUserHandler
{

	public function __construct(
		private EntityManagerInterface $em,
		private EventDispatcherInterface $ed,
	) {
	}

	public function __invoke(CreateUserCommand $command): Result
	{
		$user = new User();
		$user->username = $command->username;
		$user->email = $command->email;
		$user->password = $command->password;
		$user->createdAt = $command->createdAt;
		$user->updatedAt = $command->updatedAt;
		$user->roles = $command->roles;

		try {
			$this->em->persist($user);
			$this->em->flush();
		} catch (UniqueConstraintViolationException $e) {
			throw EntityExistsException::alreadyExists(User::class, $e);
		}

		// $this->ed->dispatch(new UserCreatedEvent($user));

		return Result::from($user);
	}

}
