<?php

declare(strict_types = 1);

namespace App\Domain\User\Database;

use App\Model\Database\Repository\AbstractRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @extends AbstractRepository<UserRepository>
 */
class UserRepository extends AbstractRepository
{

	public function __construct(
		EntityManagerInterface $em
	)
	{
		parent::__construct($em->getRepository(User::class));
	}

}
