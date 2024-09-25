# Mate

> Yummy opinionated PHP generator for web masters.

## Installation

```bash
composer require contributte/mate --dev
```

## Quickstart

1. Create `.mate.neon` in your project root.

```neon
data:
	user:
		fields:
			username: {type: string}
			email: {type: string}
			password: {type: string}
			createdAt: {type: Nette\Utils\DateTime}
			updatedAt: {type: Nette\Utils\DateTime}
```

2. Run `vendor/bin/mate` or `php mate.phar` in your project root.

```
php mate.phar craft
```

## Configuration

Under construction.
