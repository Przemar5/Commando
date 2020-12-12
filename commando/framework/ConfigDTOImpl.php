<?php

namespace Commando\Framework;

require_once './commando/framework/ConfigDTO.php';
require_once './commando/libs/ArrayTraverser.php';

use Commando\Framework\ConfigDTO;
use Commando\Libs\ArrayTraverser;

class ConfigDTOImpl extends ArrayTraverser implements ConfigDTO 
{
	public function __construct(array $data)
	{
		$this->data = $data;
	}

	public function isCommandAvailable(string $command): bool
	{
		return $this->nestedKeyChainExists(['commands', $command]);
	}

	public function getSyntax()
	{
		if (array_key_exists('syntax', $this->data))
			return $this->data['syntax'];
	}

	public function getGenericOptions()
	{
		if (array_key_exists('options', $this->data))
			return $this->data['options'];
	}

	public function isGenericOptionAvailable(string $option): bool
	{
		return in_array($option, $this->getGenericOptions() ?? []);
	}

	public function getCommandDetails(string $command)
	{
		$chain = ['commands', $command];

		return $this->getValueIfKeyChainExists($chain);
	}

	private function getValueIfKeyChainExists(array $chain)
	{
		if ($this->nestedKeyChainExists($chain))
			return $this->getValueByNestedKeyChain($chain);
	}

	public function getSpecificOptionsForCommand(string $command)
	{
		$chain = ['commands', $command, 'options'];

		return $this->getValueIfKeyChainExists($chain);
	}

	public function getOptionsForCommand(string $command)
	{
		return array_merge(
			$this->getGenericOptions() ?? [],
			$this->getSpecificOptionsForCommand($command) ?? []
		);
	}

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

	// public function getArgsOfOptionForCommand(string $option, string $command): array
	// {
	// 	$this->throwExceptionIfCommandNotAvailable($command);
		
	// 	if ($this->isOptionAvailableForCommand($option, $command)) {
	// 		return $this->commands[$command]['options'][$option]['args'];
	// 	}
	// }
}