# Mate

> Yummy opinionated PHP generator for web masters.

## Installation

```bash
composer require contributte/crafter --dev
```

## Quickstart

1. Create `crafter.neon` in your project root.

You can initialize it by running `vendor/bin/crafter init`. Or you can create it manually.

```neon
data:
	user:
		fields:
			username: {type: string}
			email: {type: string}
			password: {type: string}
			createdAt: {type: datetime}
			updatedAt: {type: datetime}
```

2. Run `vendor/bin/crafter` or `php crafter.phar` in your project root.

```
vendor/bin/crafter craft
```

## Configuration

Under construction.

## Usage

### `crafter init`

Create `crafter.neon` in your project.

### `crafter craft`

Generate files based on `crafter.neon`.

You can define:

- `--data|-k` - data structure key
- `--scope|-s` - scope of generation

```bash
vendor/bin/crafter craft -k user
vendor/bin/crafter craft -k user -s database
```

### `crafter generate`

Generate whole project based on template.

You can define:

- `--template|-t` - project template
- `--directory|-d` - output folder

```bash
vendor/bin/crafter generate -t nella -d demo
```
