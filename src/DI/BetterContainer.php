<?php declare(strict_types = 1);

namespace Contributte\Mate\DI;

use Closure;
use Nette\DI\Container;

final class BetterContainer extends Container
{

	public function service(string $class, Closure $factory): void
	{
		$this->addService($class, fn () => $this->callMethod($factory));

		$this->wiring[$class] = [0 => [$class]];
	}

}
