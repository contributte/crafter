<?php declare(strict_types = 1);

namespace Tests\Cases;

use Contributte\Tester\Toolkit;
use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';

Toolkit::test(static function (): void {
	Assert::true(true);
});
