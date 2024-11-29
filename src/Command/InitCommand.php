<?php declare(strict_types = 1);

namespace Contributte\Crafter\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
	name: 'init',
	description: 'Create crafter.neon file'
)]
final class InitCommand extends Command
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
