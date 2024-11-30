<?php declare(strict_types = 1);

namespace Contributte\Crafter\Command;

use Contributte\Crafter\Config\Loader\ConfigLoader;
use Contributte\Crafter\Utils\Validators;
use Contributte\Crafter\Worker\Crafter\CrafterContext;
use Contributte\Crafter\Worker\Crafter\CrafterWorker;
use Laravel\Prompts\TextPrompt;
use Nette\Safe;
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
		private CrafterWorker $crafterWorker,
	)
	{
		parent::__construct();
	}

	protected function configure(): void
	{
		$this->addOption('data', 'k', InputOption::VALUE_REQUIRED, 'Data key definition');
		$this->addOption('scope', 's', InputOption::VALUE_REQUIRED, 'Scope definition');
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$style = (new SymfonyStyle($input, $output));
		$ui = $style->getErrorStyle();

		// Input
		/** @var string $inputKey */
		$inputKey = $input->getOption('data');

		if (Validators::empty($inputKey)) {
			/** @var string $inputKey */
			$inputKey = (new TextPrompt(label: 'What data structure do you want to craft?', required: true))->prompt();
		}

		/** @var string $inputScope */
		$inputScope = $input->getOption('scope');

		// Vars
		$key = $inputKey;
		$cwd = Safe::getcwd();
		$scopes = Validators::empty($inputScope) ? ['default'] : [$inputScope];
		$configFile = $cwd . '/crafter.neon';

		// Config
		$config = (new ConfigLoader())
			->withCwd($cwd)
			->withFile($configFile)
			->load();

		// HUD
		$ui->title('Input');
		$ui->table([], [
			['CWD', $cwd],
			['Config', $configFile],
			['Structure', $key],
			['Presets', $config->app->preset],
			['Scopes', implode(',', $scopes)],
		]);

		// Context
		$workerContext = CrafterContext::from($config, $scopes);

		// Worker validation
		if (!$config->data->has($key)) {
			$ui->error(sprintf('Unknown data reference "%s" in crafter.neon', $key));

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
