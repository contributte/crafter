<?php declare(strict_types = 1);

namespace Contributte\Mate;

use Contributte\Mate\Command\CraftCommand;
use Symfony\Component\Console\Application;

final class Bootstrap
{

	public static function boot(): Application
	{
		$application = new Application('Mate', '1.0.0');
		$application->add(new CraftCommand());

		return $application;
	}

	public static function run(): void
	{
		self::boot()->run();
	}

}
