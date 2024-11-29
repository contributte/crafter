<?php declare(strict_types = 1);

namespace Tests\Cases\Config\Loader;

use Contributte\Crafter\Config\Loader\ConfigLoader;
use Contributte\Tester\Toolkit;
use Nette\Neon\Neon;
use Nette\Utils\Finder;
use Nette\Utils\Json;
use Tester\Assert;

require_once __DIR__ . '/../../../bootstrap.php';

Toolkit::test(static function (): void {
	foreach (Finder::findFiles('*.neon')->in(__DIR__ . '/__files__') as $file) {
		$case = Neon::decodeFile($file->getRealPath());

		$loader = (new ConfigLoader())
			->withCwd(__DIR__)
			->withConfig($case['input'])
			->load();

		Assert::match(
			Json::encode($case['output'], pretty: true),
			Json::encode($loader, pretty: true),
		);
	}
});
