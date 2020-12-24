<?php

namespace Commando\Commands\Classes;

use Commando\Commands\Classes\Command;

class ExampleCommand extends Command
{
	public function execute(): void
	{
		echo 'ok'.PHP_EOL;
	}

	public function redo(): void
	{
		//
	}
}