<?php declare(strict_types = 1);

namespace Contributte\Mate\Config;

use Nette\Schema\Expect;
use Nette\Schema\Schema;

final class MateConfig
{

	public static function schema(): Schema
	{
		return Expect::structure([
			'username' => Expect::string()->required(),
			'email' => Expect::string()->required(),
			'password' => Expect::string()->required(),
		])
			->castTo(self::class);
	}

}
