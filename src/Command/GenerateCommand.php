<?php declare(strict_types = 1);

namespace Contributte\Crafter\Command;

use Contributte\Crafter\Config\Loader\ConfigLoader;
use Contributte\Crafter\Worker\Generator\GeneratorContext;
use Contributte\Crafter\Worker\Generator\GeneratorWorker;
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
	name: 'generate',
	description: 'Generate project by defined template'
)]
final class GenerateCommand extends BaseCommand
{

	public function __construct(
		private GeneratorWorker $generatorWorker,
	)
	{
		parent::__construct();
	}

	protected function configure(): void
	{
		$this->addOption('template', 't', InputOption::VALUE_REQUIRED, 'Project template');
		$this->addOption('directory', 'd', InputOption::VALUE_REQUIRED, 'Output directory');
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$ui = (new SymfonyStyle($input, $output))->getErrorStyle();

		try {
			/** @var object{ template: string, directory: string } $options */
			$options = (new Processor())->process(
				Expect::structure([
					'template' => Expect::mixed()->assert(fn ($v) => $v !== null && $v !== '', 'Option --template|-t must be filled'),
					'directory' => Expect::mixed()->default('.')->assert(fn ($v) => $v !== null && $v !== '', 'Option --directory|-d must be filled'),
				])->otherItems(),
				$input->getOptions()
			);
		} catch (ValidationException $e) {
			foreach ($e->getMessageObjects() as $message) {
				$ui->error($message->variables['assertion']);
			}

			return Command::FAILURE;
		}

		// Input
		$template = $options->template;
		$cwd = Safe::getcwd();

		// Config
		$config = (new ConfigLoader())
			->withCwd($cwd)
			->withConfig([
				'template' => $template,
			])
			->load();

		// HUD
		$ui->title('Input');
		$ui->table([], [
			['CWD', $cwd],
			['Template', $template],
		]);

		// Context
		$generatorContext = GeneratorContext::from($config, dir: $options->directory);

		// Worker
		$result = $this->generatorWorker->execute($generatorContext, $this->createLogger($output));

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
