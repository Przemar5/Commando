<?php

declare(strict_types = 1);

namespace Commando\Models;

require_once './commando/utils/ArrayTraverser.php';
require_once './commando/models/Settings.php';

use Commando\Models\Settings;
use Commando\Utils\ArrayTraverser;

class SettingsArray extends ArrayTraverser implements Settings 
{
	public function __construct(array $data)
	{
		$this->data = $data;
	}

	public function commandExists(string $command): bool
	{
		return $this->nestedKeyChainExists(['commands', $command]);
	}

	public function getCommandArgs(string $command): array
	{
		$chain = ['commands', $command, 'args'];
		$args = $this->getNestedKeyChainValueIfArrayOrReturnEmptyArray($chain);
		$args = array_values($args);
		$args = array_filter($args, fn($a) => is_string($a));

		return $args;
	}

	private function getNestedKeyChainValueIfArrayOrReturnEmptyArray(
		array $chain
	): ?array
	{
		return $this->getNestedKeyChainValueIfTypeOfOrReturnValue(
			$chain, 'array', []
		);
	}

	private function getNestedKeyChainValueIfStringOrReturnNull(
		array $chain
	): ?string
	{
		return $this->getNestedKeyChainValueIfTypeOfOrReturnValue(
			$chain, 'string', null
		);
	}

	private function getNestedKeyChainValueIfTypeOfOrReturnValue(
		array $chain,
		string $type,
		mixed $alternative
	): mixed
	{
		if ($this->nestedKeyChainExists($chain)) {
			$result = $this->getValueByNestedKeyChain($chain);

			return (gettype($result) === $type) ? $result : $alternative;
		}

		return $alternative;
	}

	public function getCommandDescription(string $command): ?string
	{
		$chain = ['commands', $command, 'description'];

		return $this->getNestedKeyChainValueIfStringOrReturnNull($chain);
	}

	public function getCommandClass(string $command): ?string
	{
		$chain = ['commands', $command, 'class'];

		return $this->getNestedKeyChainValueIfStringOrReturnNull($chain);
	}

	public function commandOptionExists(string $command, string $option): bool
	{
		$chain = ['commands', $command, 'options', $option];

		return $this->commandExists($command) && 
			($this->nestedKeyChainExists($chain) || 
			$this->nestedKeyChainExists(['options', $option]));
	}

	public function getCommandOptionDescription(
		string $command, 
		string $option
	): ?string
	{
		if (!$this->commandExists($command))
			return null;

		$chain = ['commands', $command, 'options', $option, 'description'];
		$anotherChain = ['options', $option, 'description'];

		return $this->getNestedKeyChainValueIfStringOrReturnNull($chain) ??
			$this->getNestedKeyChainValueIfStringOrReturnNull($anotherChain);
	}

	private function getNestedKeyChainValueIfArrayOrReturnNull(
		array $chain
	): ?array
	{
		return $this->getNestedKeyChainValueIfTypeOfOrReturnValue(
			$chain, 'array', null
		);
	}

	public function getCommandOptionArgs(string $command, string $option): array
	{
		if (!$this->commandOptionExists($command, $option))
			return [];

		$chain = ['commands', $command, 'options', $option, 'args'];
		$anotherChain = ['options', $option, 'args'];

		$args = $this->getNestedKeyChainValueIfArrayOrReturnEmptyArray($chain);
		if (empty($args))
			$args = $this->getNestedKeyChainValueIfArrayOrReturnEmptyArray(
				$anotherChain
			);

		$args = array_values($args);
		$args = array_filter($args, fn($a) => is_string($a));

		return $args ?? [];
	}
}