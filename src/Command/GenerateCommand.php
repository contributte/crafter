<?php declare(strict_types = 1);

namespace Contributte\Crafter\Command;

use Contributte\Crafter\Config\Loader\ConfigLoader;
use Contributte\Crafter\Utils\Validators;
use Contributte\Crafter\Worker\Generator\GeneratorContext;
use Contributte\Crafter\Worker\Generator\GeneratorWorker;
use Laravel\Prompts\SelectPrompt;
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

		// Input
		/** @var string $inputTemplate */
		$inputTemplate = $input->getOption('template');

		if (Validators::empty($inputTemplate)) {
			/** @var string $inputTemplate */
			$inputTemplate = (new SelectPrompt(
				label: 'What project template do you want to use?',
				options: [
					'nella' => 'Nella project',
					'nella-mini' => 'Nella (mini) project',
					'nette' => 'Nette ',
					'doctrine' => 'Nette + Doctrine (coming soon)',
					'messenger' => 'Symfony Messenger (coming soon)',
				],
				required: true
			))->prompt();
		}

		/** @var string $inputDirectory */
		$inputDirectory = $input->getOption('directory');

		if (Validators::empty($inputDirectory)) {
			/** @var string $inputDirectory */
			$inputDirectory = (new TextPrompt(label: 'Where to create project (folder)?', default: 'demo', required: true))->prompt();
		}

		// Vars
		$template = $inputTemplate;
		$directory = $inputDirectory;
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
		$generatorContext = GeneratorContext::from($config, dir: $directory);

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
