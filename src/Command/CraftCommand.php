<?php declare(strict_types = 1);

namespace Contributte\Mate\Command;

use Contributte\Mate\Config\Loader\ConfigLoader;
use Contributte\Mate\Crafter\Worker\CrafterWorker;
use Contributte\Mate\Crafter\Worker\WorkerContextFactory;
use Nette\Safe;
use Nette\Schema\Expect;
use Nette\Schema\Processor;
use Nette\Schema\ValidationException;
use Nette\Utils\Arrays;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
	name: 'craft',
	description: 'Craft classes by defined config'
)]
final class CraftCommand extends BaseCommand
{

	public function __construct(
		private ConfigLoader $configLoader,
		private CrafterWorker $crafterWorker,
	)
	{
		parent::__construct();
	}

	protected function configure(): void
	{
		$this->addOption('struct', 's', InputOption::VALUE_REQUIRED, 'Structure definition');
		$this->addOption('scope', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Scope definition');
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$ui = (new SymfonyStyle($input, $output))->getErrorStyle();

		try {
			/** @var object{ struct: string, scope: string[] } $options */
			$options = (new Processor())->process(Expect::structure([
				'struct' => Expect::mixed()->assert(fn ($v) => $v !== null && $v !== '', 'Option --struct|-s must be filled'),
				'scope' => Expect::arrayOf('string')->default([]),
			])->otherItems(), $input->getOptions());
		} catch (ValidationException $e) {
			foreach ($e->getMessageObjects() as $message) {
				$ui->error($message->variables['assertion']);
			}

			return Command::FAILURE;
		}

		// Input
		$struct = $options->struct;
		$cwd = Safe::getcwd();
		$configFile = $cwd . '/.mate.neon';

		// Config
		$config = $this->configLoader->load($cwd, $cwd . '/.mate.neon');

		// HUD
		$ui->title('Input');
		$ui->table([], [
			['CWD', $cwd],
			['Config', $configFile],
			['Struct', $struct],
			['Presets', implode(',', $config->mate->presets)],
			['Scopes', implode(',', $options->scope)],
		]);

		// Context
		$workerContextFactory = new WorkerContextFactory();
		$workerContextFactory->withScopes($options->scope);
		$workerContext = $workerContextFactory->from($config);

		// Worker validation
		if (!$config->structs->has($struct)) {
			$ui->error(sprintf('Unknown struct reference "%s" in .mate.neon', $struct));

			return Command::FAILURE;
		}

		// Worker
		$result = $this->crafterWorker->execute($workerContext, $this->createLogger($output));

		// HUD
		$ui->title('Crafted');

		foreach ($result->items as $item) {
			if ($item['state'] === 'skipped') {
				continue;
			}

			$ui->block($item['crafter']);
			$ui->table([], [
				['Input', $item['input']],
				['Output', $item['output']],
				['Note', $item['note']],
			]);
		}

		$ui->info(sprintf('Crafted: %d files', count(Arrays::filter($result->items, fn ($item) => $item['state'] === 'crafted'))));

		return Command::SUCCESS;
	}

}
