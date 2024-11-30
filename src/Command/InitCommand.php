<?php declare(strict_types = 1);

namespace Contributte\Crafter\Command;

use Laravel\Prompts\ConfirmPrompt;
use Laravel\Prompts\SelectPrompt;
use Laravel\Prompts\TextPrompt;
use Nette\Neon\Neon;
use Nette\Safe;
use Nette\Utils\FileSystem;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

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
		$ui = (new SymfonyStyle($input, $output))->getErrorStyle();

		// Inputs
		/** @var string $inputDirectory */
		$inputDirectory = (new TextPrompt(
			label: 'What output directory do you want to use?',
			default: 'src',
			required: true
		))->prompt();

		/** @var string $inputNamespace */
		$inputNamespace = (new TextPrompt(
			label: 'What root namespace do you want to use?',
			default: 'App',
			required: true
		))->prompt();

		/** @var string $inputPreset */
		$inputPreset = (new SelectPrompt(
			label: 'What preset do you want to use?',
			options: [
				'default' => 'no preset',
				'nette' => 'nette web application',
				'fx' => "f3l1x's nette web application",
			],
			default: 'default',
			required: true
		))->prompt();

		/** @var bool $inputDataExample */
		$inputDataExample = (new ConfirmPrompt(
			label: 'Do you want to data structure example?',
			yes: 'Yes example',
			no: 'No example',
			required: true
		))->prompt();

		// Vars
		$cwd = Safe::getcwd();

		$config = [
			'version' => '1',
			'dir' => $inputDirectory,
			'namespace' => $inputNamespace,
			'preset' => $inputPreset,
		];

		if ($inputDataExample) {
			$config['data'] = [
				'user' => [
					'fields' => [
						'username' => [
							'type' => 'string',
						],
						'email' => [
							'type' => 'string',
						],
					],
				],
			];
		}

		FileSystem::write($cwd . '/crafter.neon', Neon::encode($config, blockMode: true));

		$ui->success('crafter.neon file has been created');

		return Command::SUCCESS;
	}

}
