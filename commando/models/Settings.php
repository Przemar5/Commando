<?php

namespace Commando\Models;

use Commando\Utils\Traverser;

interface Settings
{
	public function commandExists(string $command): bool;//
	
	public function getCommandArgs(string $command): array;//
	
	public function getCommandDescription(string $command): ?string;//
	
	public function getCommandClass(string $command): ?string;//
	
	public function commandOptionExists(string $command, string $option): bool;//
	
	public function getCommandOptionArgs(
		string $command, 
		string $option
	): array;//
		
	public function getCommandOptionDescription(
		string $command, 
		string $option
	): ?string;//
}