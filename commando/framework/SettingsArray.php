<?php

declare(strict_types = 1);

namespace Commando\Framework;

require_once './commando/framework/Settings.php';
require_once './commando/libs/ArrayTraverser.php';

use Commando\Framework\Settings;
use Commando\Libs\ArrayTraverser;

class SettingsArray extends ArrayTraverser //implements Settings 
{
	public function __construct(array $data)
	{
		$this->data = $data;
	}

	public function getSyntax(): ?string
	{
		return $this->getNestedKeyChainValueIfStringOrReturnNull(['syntax']);
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

	public function isCommandAvailable(string $command): bool
	{
		return $this->nestedKeyChainExists(['commands', $command]);
	}

	public function getCommandSyntax(string $command): ?string
	{
		$chain = ['commands', $command, 'syntax'];

		return $this->getNestedKeyChainValueIfStringOrReturnNull($chain);
	}

	public function getCommandDescription(string $command): ?string
	{
		$chain = ['commands', $command, 'description'];

		return $this->getNestedKeyChainValueIfStringOrReturnNull($chain);
	}

	// public function commandOptionExists(string $command, string $option): bool
	// {
	// 	$chain = ['commands', $command, 'options', $option];

	// 	return $this->nestedKeyChainExists($chain);
	// }

	public function getCommandOptionDescription(
		string $command, 
		string $option
	): ?string
	{
		$chain = ['commands', $command, 'options', $option, 'description'];

		$this->getNestedKeyChainValueIfStringOrReturnNull($chain);
	}

	public function getCommandArgs(string $command): ?array
	{
		$chain = ['commands', $command, 'args'];

		$this->getNestedKeyChainValueIfArrayOrReturnNull($chain);
	}

	private function getNestedKeyChainValueIfArrayOrReturnNull(
		array $chain
	): ?string
	{
		return $this->getNestedKeyChainValueIfTypeOfOrReturnValue(
			$chain, 'array', null
		);
	}

	// public function getCommandOptions(string $command): ?array
	// {
	// 	if ($this->isCommandAvailable(string $command)) {
	// 		return array_merge(
	// 			$this->data['options'],
	// 			$this->getValueByNestedKeyChain([''])
	// 		);
	// 	}
	// }

	// public function getCommandDescription(string $command): ?string;
	
	// public function getCommandArgs(string $command): ?array;
	
	// public function isOptionAvailableForCommand(
	// 	string $option, 
	// 	string $command
	// ): ?string;
	
	// public function getDescriptionOfCommandOption(
	// 	string $option, 
	// 	string $command
	// ): ?string;
	
	// public function getArgsOfCommandOption(
	// 	string $option, 
	// 	string $command
	// ): ?array;

	// private function getGenericOptions(): array
	// {
	// 	return (array_key_exists('options', $this->data))
	// 		? $this->data['options'] : [];
	// }

	// private function isGenericOptionAvailable(string $option): bool
	// {
	// 	return in_array($option, 
	// 		array_keys($this->getGenericOptions()) ?? []);
	// }

	// public function getCommandDetails(string $command): ?array
	// {
	// 	$chain = ['commands', $command];
	// 	$details = $this->getValueIfKeyChainExists($chain);

	// 	return is_array($details) ? $details : null;
	// }

	// public function getSpecificOptionsForCommand(string $command): array
	// {
	// 	$chain = ['commands', $command, 'options'];

	// 	return $this->getValueIfKeyChainExists($chain) ?? [];
	// }

	// public function getOptionsForCommand(string $command): array
	// {
	// 	return array_merge(
	// 		$this->getGenericOptions(),
	// 		$this->getSpecificOptionsForCommand($command)
	// 	);
	// }

	// public function getDescriptionForCommand(string $command): ?string
	// {
	// 	$chain = ['commands', $command, 'description'];
	// 	$result = $this->getValueIfKeyChainExists($chain);

	// 	return (isset($result) && (is_string($result) || is_null($result))) 
	// 		? $result : null;
	// }

	// public function getGenericOptionDetails(string $option): ?array
	// {
	// 	if ($this->isGenericOptionAvailable($option)) {
	// 		$details = $this->getValueByNestedKeyChain(['options', $option]);

	// 		return (is_array($details)) ? $details : null; 
	// 	}

	// 	return null;
	// }

	// public function isOptionAvailableForCommand(
	// 	string $option,
	// 	string $command
	// ): bool
	// {
	// 	return (bool) $this->isCommandAvailable($command) && 
	// 		array_key_exists($option, $this->getOptionsForCommand($command));
	// }

	// public function getOptionDetailsForCommand(
	// 	string $option, 
	// 	string $command
	// ): ?array
	// {
	// 	if ($this->isOptionAvailableForCommand($option, $command)) {
	// 		$details = $this->getValueByNestedKeyChain(['options', $option]);

	// 		return (is_array($details)) ? $details : null;
	// 	}

	// 	return null;
	// }

	// public function getArgsForOptionForCommand(
	// 	string $option, 
	// 	string $command
	// ): array
	// {
	// 	if ($this->isOptionAvailableForCommand($option, $command)) {
	// 		$args = $this->getOptionDetailsForCommand(
	// 			$option, $command
	// 		)['args'] ?? [];

	// 		// var_dump($this->getOptionDetailsForCommand($option, $command));
	// 		// die;

	// 		return is_array($args) ? $args : [];
	// 	}

	// 	return [];
	// }

	// public function isOptionAvailableForCommand(string $option, string $command): bool
	// {
	// 	if ($this->isCommandAvailable($command)) {
	// 		$genericOptions = $this->
	// 	}
	// 	return $this->checkIfExistsChainForData();
	// }

	// public function getOptionsForCommand(string $command)
	// {
	// 	$chain = ['commands', $command];

	// 	if ($this->nestedKeyChainExists($chain)) {
	// 		$genericOptions = $this->getValueByNestedKeyChain($chain);
	// 		$specificOptions = $this->getValueByNestedKeyChain(['options']);

	// 		return array_merge();
	// 	}
	// }

	// private function mergeMultipleKeyChainResults(array $chain): array
	// {

	// }

	// private function throwExceptionIfCommandNotAvailable(string $command): void
	// {
	// 	if (!$this->isCommandAvailable($command))
	// 		throw new \Exception("Command `$command` is not available.");
	// }

	// public function getSyntaxForCommandOrThrowException(string $command): string
	// {
	// 	$this->throwExceptionIfCommandNotAvailable($command);
	// 	$this->throwExceptionIfNoNamedKeyForCommandMissing('syntax', $command);

	// 	$syntax = $this->commands[$command]['syntax'] ?? null;

	// 	$this->throwExceptionIfSyntaxIsNotString($syntax);

	// 	return $syntax;
	// }

	// private function throwExceptionIfSyntaxIsNotString($syntax): void
	// {
	// 	$type = gettype($syntax);

	// 	if ($type !== 'string')
	// 		throw new \Exception("Syntax must be string, given '$type'.");
	// }

	// private function throwExceptionIfNoNamedKeyForCommandMissing(
	// 	string $name,
	// 	string $command
	// ): void
	// {
	// 	if (!isset($this->commands[$command][$name]))
	// 		throw new \Exception(
	// 			"Parameter '$name' for command '$command' is missing."
	// 		);
	// }
}