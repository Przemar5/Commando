<?php

namespace Commando\Commands\Classes;

abstract class Command
{
	protected array $args = [];
	protected array $options = [];

	abstract public function execute(): void;

	abstract public function redo(): void;

	public function setArgs(array $args): void
	{
		$this->args = $args;
	}

	public function setOptions(array $options): void
	{
		$this->options = $options;
	}
}