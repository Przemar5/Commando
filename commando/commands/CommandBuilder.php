<?php

namespace Commando\Commands;

use Commando\Commands\Classes\Command;

class CommandBuilder
{
	private ?Command $command = null;
	private string $commandsNamespace = '\Commando\Commands\Classes\\';

	public function init(): void
	{
		$this->command = null;
	}

	public function setCommandNamespace(string $namespace): void
	{
		$this->commandsNamespace = $namespace;
	}

	public function setClass(string $class): void
	{
		$class = $this->commandsNamespace . $class;
		
		if (class_exists($class))
			$this->command = new $class();
	}

	public function setArgs(array $args): void
	{
		if (!is_null($this->command))
			$this->command->setArgs($args);
	}

	public function setOptions(array $options): void
	{
		if (!is_null($this->command))
			$this->command->setOptions($options);
	}

	public function getResult(): Command
	{
		return $this->command;
	}
}