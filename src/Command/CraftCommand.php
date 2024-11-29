<?php declare(strict_types = 1);

namespace Contributte\Crafter\Command;

use Contributte\Crafter\Config\Loader\ConfigLoader;
use Contributte\Crafter\Worker\Crafter\CrafterContext;
use Contributte\Crafter\Worker\Crafter\CrafterWorker;
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
		private CrafterWorker $crafterWorker,
	)
	{
		parent::__construct();
	}

	protected function configure(): void
	{
		$this->addOption('data', 'k', InputOption::VALUE_REQUIRED, 'Data key definition');
		$this->addOption('scope', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Scope definition');
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$ui = (new SymfonyStyle($input, $output))->getErrorStyle();

		try {
			/** @var object{ data: string, scope: string[] } $options */
			$options = (new Processor())->process(
				Expect::structure([
					'data' => Expect::mixed()->assert(fn ($v) => $v !== null && $v !== '', 'Option --data|-k must be filled'),
					'scope' => Expect::arrayOf('string'),
				])->otherItems()
					->before(function (array $v) {
						$v['scope'] = $v['scope'] === [] ? ['default'] : $v['scope'];

						return $v;
					}),
				$input->getOptions()
			);
		} catch (ValidationException $e) {
			foreach ($e->getMessageObjects() as $message) {
				$ui->error($message->variables['assertion']);
			}

			return Command::FAILURE;
		}

		// Input
		$key = $options->data;
		$cwd = Safe::getcwd();
		$scopes = $options->scope ?? ['default'];
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
