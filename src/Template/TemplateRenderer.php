<?php declare(strict_types = 1);

namespace Contributte\Crafter\Template;

use Latte\ContentType;
use Latte\Engine;
use Latte\Loaders\StringLoader;
use Nette\Utils\FileSystem;

final class TemplateRenderer
{

	private Engine $latte;

	public function __construct()
	{
		$this->latte = new Engine();
		$this->latte->setLoader(new StringLoader());
		$this->latte->setStrictTypes();
		$this->latte->setStrictParsing();
		$this->latte->setContentType(ContentType::Text);
	}

	/**
	 * @param array<mixed> $params
	 */
	public function render(string $string, array $params = []): string
	{
		return $this->latte->renderToString($string, $params);
	}

	/**
	 * @param array<mixed> $params
	 */
	public function renderPhp(string $file, array $params = []): string
	{
		return $this->render(FileSystem::read($file), $params);
	}

	/**
	 * @param array<mixed> $params
	 */
	public function renderLatte(string $file, array $params = []): string
	{
		return $this->render('{syntax double}' . FileSystem::read($file), $params);
	}

}
