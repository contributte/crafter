<?php declare(strict_types = 1);

namespace Contributte\Crafter\Command;

use Psr\Log\LogLevel;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\OutputInterface;

abstract class BaseCommand extends Command
{

	protected function createLogger(OutputInterface $output): ConsoleLogger
	{
		return new ConsoleLogger($output, [
			LogLevel::EMERGENCY => OutputInterface::VERBOSITY_NORMAL,
			LogLevel::ALERT => OutputInterface::VERBOSITY_NORMAL,
			LogLevel::CRITICAL => OutputInterface::VERBOSITY_NORMAL,
			LogLevel::ERROR => OutputInterface::VERBOSITY_NORMAL,
			LogLevel::WARNING => OutputInterface::VERBOSITY_NORMAL,
			LogLevel::NOTICE => OutputInterface::VERBOSITY_NORMAL,
			LogLevel::INFO => OutputInterface::VERBOSITY_NORMAL,
			LogLevel::DEBUG => OutputInterface::VERBOSITY_NORMAL,
		]);
	}

}
