<?php declare(strict_types = 1);

namespace Contributte\Mate\Command;

use Contributte\Mate\Generator\CommandGenerator;
use Contributte\Mate\Generator\HandlerGenerator;
use Nette\Neon\Neon;
use Nette\Utils\FileSystem;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
	name: 'craft',
	description: 'Craft classes by defined config'
)]
final class CraftCommand extends Command
{

	protected function configure(): void
	{
		$this->addOption('data', 'd', InputOption::VALUE_REQUIRED, 'Data structure reference');
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$cwd = getcwd();

		// Load config
		$file = FileSystem::read($cwd . '/.mate.neon');

		/** @var array{data: array<string, array{fields: array<array{type: string}>}>} $config */
		$config = Neon::decode($file);

		// Validation
		/** @var string|false|null $dataKey */
		$dataKey = $input->getOption('data');

		if ($dataKey === null || $dataKey === false) {
			$output->writeln('Missing --data option');

			return Command::FAILURE;
		}

		if (!isset($config['data'][$dataKey])) {
			$output->writeln(sprintf('Unknown data reference "%s" in .mate.neon', $dataKey));

			return Command::FAILURE;
		}

		// Entity
		$dataClass = ucfirst($dataKey);

		// Generators
		$commandGenerator = new CommandGenerator();

		foreach ($config['data'] as $data) {
			$filename = $cwd . sprintf('/app/Domain/%s/Create%sCommand.php', $dataClass, $dataClass);
			$generatedClass = $commandGenerator->generate(
				namespace: sprintf('App\Domain\%s', $dataClass),
				commandClass: sprintf('App\Domain\%s\Create%sCommand', $dataClass, $dataClass),
				fields: $data['fields']
			);
			FileSystem::write($filename, $generatedClass);
		}

		$handlerGenerator = new HandlerGenerator();

		foreach ($config['data'] as $data) {
			$filename = $cwd . sprintf('/app/Domain/%s/Create%sHandler.php', $dataClass, $dataClass);
			$generatedClass = $handlerGenerator->generate(
				namespace: sprintf('App\Domain\%s', $dataClass),
				handlerClass: sprintf('App\Domain\%s\Create%sHandler', $dataClass, $dataClass),
				commandClass: sprintf('App\Domain\%s\Create%sCommand', $dataClass, $dataClass),
				entityClass: $dataClass,
				fields: $data['fields']
			);
			FileSystem::write($filename, $generatedClass);
		}

		return Command::SUCCESS;
	}

}
