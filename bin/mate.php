<?php declare (strict_types = 1);

if (
	!(is_file($file = __DIR__ . '/../vendor/autoload.php') && include $file) &&
	!(is_file($file = __DIR__ . '/../../../autoload.php') && include $file)
) {
	fwrite(STDERR, "Install packages using Composer.\n");
	exit(1);
}

Contributte\Mate\Bootstrap::run();
