<?php

require_once './vendor/autoload.php';
require_once './commando/framework/CommandLineParser.php';

use \Commando\Framework\CommandLineParser as CommandLineParser;

class CommandLineParserTest extends PHPUnit\Framework\TestCase
{
	private CommandLineParser $clp;

	// public function setUp(): void
	// {
	// 	$this->clp = new CommandLineParser();
	// }

	/**@test */
	public function testIfWorks()
	{
		$this->assertTrue(true);
	}

	// /**@test */
	// public function testIfCommandLineParserIsInstantiable()
	// {
	// 	$this->assertIsObject($this->clp);
	// }
}