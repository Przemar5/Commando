<?php

require_once './commando/commands/CommandBuilder.php';
require_once './commando/models/SettingsArray.php';

use Commando\Commands\CommandBuilder;
use Commando\Models\SettingsArray;

class CommandBuilderTest extends PHPUnit\Framework\TestCase
{
	/**
	 * @test
	 */
	public function testIfWorks()
	{
		$this->assertTrue(true);
	}

	// /**
	//  * @test
	//  */
	// public function testIfWorksCorrectly(
	// 	string $class,
	// 	array $args,
	// 	array $options,
	// 	mixed $expectedResult
	// )
	// {
	// 	$builder = new CommandBuilder();
	// 	$builder->init();
	// 	$builder->setClass($class);
	// 	$builder->setArgs($args);
	// 	$builder->setOptions($options);
	// 	$command = $builder->getResult();

	// 	$this->assertTrue($command === $expectedResult);
	// }

	// public function builderDataProvider(): array
	// {
	// 	return [
	// 		// ['ExampleCommand', ],
	// 	];
	// }
}