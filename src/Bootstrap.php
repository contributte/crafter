<?php declare(strict_types = 1);

namespace Contributte\Mate;

use Contributte\Mate\Command\CraftCommand;
use Contributte\Mate\Config\Loader\ConfigLoader;
use Contributte\Mate\Crafter\Worker\CrafterWorker;
use Contributte\Mate\DI\BetterContainer;
use Contributte\Mate\Template\TemplateRenderer;
use Symfony\Component\Console\Application;

final class Bootstrap
{

	public static function boot(): Application
	{
		$container = new BetterContainer();
		$container->service(ConfigLoader::class, fn (): ConfigLoader => new ConfigLoader());
		$container->service(TemplateRenderer::class, fn (): TemplateRenderer => new TemplateRenderer());
		$container->service(CrafterWorker::class, fn (TemplateRenderer $templateRenderer): CrafterWorker => new CrafterWorker($templateRenderer));

		$application = new Application('Mate', '0.1.0');
		$application->add($container->createInstance(CraftCommand::class));

		return $application;
	}

	public static function run(): void
	{
		self::boot()->run();
	}

}
