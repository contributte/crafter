![](https://heatbadger.now.sh/github/readme/contributte/crafter/)

<p align=center>
	<a href="https://github.com/contributte/crafter/actions"><img src="https://badgen.net/github/checks/contributte/crafter/master?cache=300"></a>
	<a href="https://coveralls.io/r/contributte/crafter"><img src="https://badgen.net/coveralls/c/github/contributte/crafter?cache=300"></a>
	<a href="https://packagist.org/packages/contributte/crafter"><img src="https://badgen.net/packagist/dm/contributte/crafter"></a>
	<a href="https://packagist.org/packages/contributte/crafter"><img src="https://badgen.net/packagist/v/contributte/crafter"></a>
</p>
<p align=center>
	<a href="https://packagist.org/packages/contributte/crafter"><img src="https://badgen.net/packagist/php/contributte/crafter"></a>
	<a href="https://github.com/contributte/crafter"><img src="https://badgen.net/github/license/contributte/crafter"></a>
	<a href="https://bit.ly/ctteg"><img src="https://badgen.net/badge/support/gitter/cyan"></a>
	<a href="https://bit.ly/cttfo"><img src="https://badgen.net/badge/support/forum/yellow"></a>
	<a href="https://contributte.org/partners.html"><img src="https://badgen.net/badge/become/a%20patron/F96854"></a>
</p>

<p align=center>
Website 🚀 <a href="https://contributte.org">contributte.org</a> | Contact 👨🏻‍💻 <a href="https://f3l1x.io">f3l1x.io</a> | Twitter 🐦 <a href="https://twitter.com/contributte">@contributte</a>
</p>

Crafter is a yummy opinionated PHP generator for web masters.

## Versions

| State  | Version | Branch   | PHP     |
|--------|---------|----------|---------|
| dev    | `^0.1`  | `master` | `>=8.2` |
| stable | `^0.1`  | `master` | `>=8.2` |

## Installation

To install latest version of `contributte/crafter` use [Composer](https://getcomposer.org).

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

```bash
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

## Development

See [how to contribute](https://contributte.org/contributing.html) to this package.

This package is currently maintained by these authors.

<a href="https://github.com/f3l1x">
  <img width="80" height="80" src="https://avatars2.githubusercontent.com/u/538058?v=3&s=80">
</a>

-----

Consider to [support](https://contributte.org/partners.html) **contributte** development team.
Also thank you for using this package.
