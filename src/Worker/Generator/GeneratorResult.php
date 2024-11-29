<?php declare(strict_types = 1);

namespace Contributte\Crafter\Worker\Generator;

final class GeneratorResult
{

	/** @var array<array{crafter: string, state: string, note: string|null, input: string|null, output: string|null}> */
	public array $items = [];

	public function add(
		string $crafter,
		string $state,
		string|null $note = null,
		string|null $input = null,
		string|null $output = null,
	): void
	{
		$this->items[] = [
			'crafter' => $crafter,
			'state' => $state,
			'note' => $note,
			'input' => $input,
			'output' => $output,
		];
	}

}
