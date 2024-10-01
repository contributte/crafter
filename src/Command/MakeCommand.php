<?php declare(strict_types = 1);

namespace Contributte\Mate\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
	name: 'make',
	description: 'Create class by defined config'
)]
final class MakeCommand extends Command
{

	public function __construct()
	{
		parent::__construct();
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		return Command::SUCCESS;
	}

}
