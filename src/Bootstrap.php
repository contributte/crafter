<?php declare(strict_types = 1);

namespace Contributte\Crafter;

use Contributte\Crafter\Command\CraftCommand;
use Contributte\Crafter\Command\GenerateCommand;
use Contributte\Crafter\Command\InitCommand;
use Contributte\Crafter\DI\BetterContainer;
use Contributte\Crafter\Template\TemplateRenderer;
use Contributte\Crafter\Worker\Crafter\CrafterWorker;
use Contributte\Crafter\Worker\Crafter\Resolver\Latte\LatteResolver;
use Contributte\Crafter\Worker\Crafter\Resolver\Php\PhpResolver;
use Contributte\Crafter\Worker\Generator\GeneratorWorker;
use Contributte\Crafter\Worker\Generator\Resolver\Raw\RawResolver;
use Symfony\Component\Console\Application;

final class Bootstrap
{

	public static function boot(): Application
	{
		$container = new BetterContainer();

		// Template
		$container->service(TemplateRenderer::class, fn (): TemplateRenderer => new TemplateRenderer());

		// Generator worker
		$container->service(GeneratorWorker::class, fn (RawResolver $rawResolver): GeneratorWorker => new GeneratorWorker($rawResolver));
		$container->service(RawResolver::class, fn (TemplateRenderer $templateRenderer): RawResolver => new RawResolver($templateRenderer));

		// Crafter worker
		$container->service(CrafterWorker::class, fn (PhpResolver $phpResolver, LatteResolver $latteResolver): CrafterWorker => new CrafterWorker($phpResolver, $latteResolver));
		$container->service(PhpResolver::class, fn (TemplateRenderer $templateRenderer): PhpResolver => new PhpResolver($templateRenderer));
		$container->service(LatteResolver::class, fn (TemplateRenderer $templateRenderer): LatteResolver => new LatteResolver($templateRenderer));

		// Symfony Console
		$application = new Application('Crafter', 'magic');
		$application->add($container->createInstance(CraftCommand::class));
		$application->add($container->createInstance(GenerateCommand::class));
		$application->add($container->createInstance(InitCommand::class));

		return $application;
	}

	public static function run(): void
	{
		self::boot()->run();
	}

}
