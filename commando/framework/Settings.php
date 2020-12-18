<?php

namespace Commando\Framework;

use Commando\Libs\Traverser;

interface Settings
{
	public function getSyntax(): ?string;
	
	public function isCommandAvailable(string $command): ?string;
	
	public function getCommandSyntax(string $command): ?string;
	
	public function getCommandDescription(string $command): ?string;
	
	public function commandOptionExists(string $option, string $command): ?string;

	public function getCommandArgs(string $command): ?array;
		
	public function getCommandOptionDescription(
		string $option, 
		string $command
	): ?string;
	
	public function getCommandOptionArgs(
		string $option, 
		string $command
	): ?array;
}