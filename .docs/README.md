# Mate

> Yummy opinionated PHP generator for web masters.

## Installation

```bash
composer require contributte/mate --dev
```

## Quickstart

1. Create `.mate.neon` in your project root.

You can initialize it by running `vendor/bin/mate init`. Or you can create it manually.

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
vendor/bin/mate craft
```

## Configuration

Under construction.

## Usage

### `mate init`

Create `.mate.neon` in your project.

### `mate craft`

Generate files based on `.mate.neon`.

```bash
vendor/bin/mate craft --struct user
```

```bash
vendor/bin/mate craft --struct user --crafter=entity
vendor/bin/mate craft --struct user --crafter=repository

vendor/bin/mate craft --struct user --crafter=bus --mode=create
vendor/bin/mate craft --struct user --crafter=bus --mode=update
vendor/bin/mate craft --struct user --crafter=bus --mode=delete
vendor/bin/mate craft --struct user --crafter=bus --mode=list
vendor/bin/mate craft --struct user --crafter=bus --mode=get

vendor/bin/mate craft --struct user --crafter=api --mode=create
vendor/bin/mate craft --struct user --crafter=api --mode=update
vendor/bin/mate craft --struct user --crafter=api --mode=delete
vendor/bin/mate craft --struct user --crafter=api --mode=list
vendor/bin/mate craft --struct user --crafter=api --mode=get
```

### `mate generate`

Generate whole project based on `.mate.neon`.

```bash
vendor/bin/mate generate --template api
```
