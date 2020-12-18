<?php

declare(strict_types = 1);
// namespace Tests\Commando\Framework;

require_once './commando/framework/SettingsArray.php';
require_once './commando/libs/ArrayTraverser.php';

use \Commando\Framework\SettingsArray;
use \Commando\Libs\ArrayTraverser;

class SettingsArrayTest extends PHPUnit\Framework\TestCase
{
	public array $data;
	public SettingsArray $settings;

	// public function setUp(): void
	// {
	// 	$this->configDTO = 
	// }

	/**@test */
	public function testIfWorks()
	{
		$this->assertTrue(true);
	}

	public function validConstructorData1()
	{
		return [
			'syntax' => '[options] [commands]',
			'options' => [
				'--first' => null,
				'--second' => [],
				'--some-option' => 'details',
			],
			'commands' => [
				'example:command' => [
					'syntax' => '[self] [arg1 ,[arg2]]',
					'description' => 'example command description',
					'options' => [
						'--third' => null,
						'--fourth' => [
							'descripttion' => null,
							'args' => null,
						],
						'--fifth' => [
							'decsription' => 'some desc',
							'args' => [
								'optArg1' => null,
								'optArg2' => null,
							],
						],
					],
					'args' => [
						'commArgName',
						'commArgValue',
					],
				],
				'next:example' => [
					'syntax' => '[self]',
					'options' => null,
					'args' => null,
				],
				'third-command' => [
					'syntax' => 1,
					'description' => null,
				],
				'fourth:command' => [
					'description' => 1,
				],
			],
		];
	}

	public function instantiateSettingsWithValidData()
	{
		$this->settings = new SettingsArray(
			$this->validConstructorData1()
		);
	}

	/**@test */
	public function testIfIsInstantiable()
	{
		$this->instantiateSettingsWithValidData();

		$this->assertIsObject($this->settings);
	}

	// isCommandAvailable tests

	/**
	 * @test
	 * @dataProvider isCommandAvailableDataProvider
	 */
	public function testIfIsCommandAvailableReturnsCorrectResult(
		$settings, 
		string $command,
		bool $expected
	)
	{
		$result = $settings->isCommandAvailable($command);

		$this->assertTrue($result === $expected);
	}

	public function isCommandAvailableDataProvider()
	{
		$settings = new SettingsArray([
			'commands' => [
				'first:command' => null,
				'second:command' => [],
				'third:command',
			],
		]);

		return [
			[$settings, 'first:command', true],
			[$settings, 'next:example', false],
			[$settings, 'third:command', false],
		];
	}

	// getSyntax tests

	/**
	 * @test
	 * @dataProvider getSyntaxDataProvider
	 */
	public function testIfGetSyntaxReturnsCorrectResultWhenExists(
		SettingsArray $settings,
		mixed $expected
	)
	{
		$syntax = $settings->getSyntax();

		$this->assertEquals($syntax, $expected);
	}

	public function getSyntaxDataProvider(): array
	{
		return [
			[new SettingsArray(['syntax' => '?options command']), '?options command'],
			[new SettingsArray(['syntax' => '']), ''],
			[new SettingsArray(['syntax' => 1]), null],
			[new SettingsArray(['syntax' => null]), null],
			[new SettingsArray([]), null],
			[new SettingsArray(['syntax']), null],
		];
	}

	// getCommandSyntax tests

	/**
	 * @test
	 * @dataProvider getCommandSyntaxDataProvider
	 */
	public function testIfGetCommandSyntaxReturnsCorrectResult(
		SettingsArray $settings,
		string $command,
		mixed $expected
	)
	{
		$result = $settings->getCommandSyntax($command);

		$this->assertTrue($result === $expected);
	}

	public function getCommandSyntaxDataProvider(): array
	{
		$settings = new SettingsArray([
			'commands' => [
				'first:command' => null,
				'second:command' => ['syntax'],
				'third:command' => ['syntax' => null],
				'fourth:command' => ['syntax' => ''],
				'fifth:command' => ['syntax' => 'command arg1 ?arg2'],
				'sixth:command' => ['syntax' => 45],
				'seventh:command' => ['syntax' => []],
			],
		]);

		return [
			[$settings, 'first:command', null],
			[$settings, 'second:command', null],
			[$settings, 'third:command', null],
			[$settings, 'fourth:command', ''],
			[$settings, 'fifth:command', 'command arg1 ?arg2'],
			[$settings, 'sixth:command', null],
			[$settings, 'seventh:command', null],
		];
	}

	// getCommandDescription tests

	/**
	 * @test
	 * @dataProvider getCommandDescriptionDataProvider
	 */
	public function testIfGetCommandDescriptionReturnsCorrectResult(
		SettingsArray $settings,
		string $command,
		mixed $expected
	)
	{
		$result = $settings->getCommandDescription($command);

		$this->assertTrue($result === $expected);
	}

	public function getCommandDescriptionDataProvider(): array
	{
		$settings = new SettingsArray([
			'commands' => [
				'first:command' => null,
				'second:command' => ['description'],
				'third:command' => ['description' => null],
				'fourth:command' => ['description' => ''],
				'fifth:command' => ['description' => 'some example desc'],
				'sixth:command' => ['description' => 45],
				'seventh:command' => ['description' => []],
			],
		]);

		return [
			[$settings, 'first:command', null],
			[$settings, 'second:command', null],
			[$settings, 'third:command', null],
			[$settings, 'fourth:command', ''],
			[$settings, 'fifth:command', 'some example desc'],
			[$settings, 'sixth:command', null],
			[$settings, 'seventh:command', null],
		];
	}

	// commandOptionExists tests

	/**
	 * @test
	 * @dataProvider commandOptionExistsDataProvider
	 */
	public function testIfCommandOptionExistsReturnsCorrectResult(
		SettingsArray $settings,
		string $command,
		string $option,
		bool $expected
	)
	{
		$result = $settings->commandOptionExists($command, $option);

		$this->assertTrue($result === $expected);
	}

	public function commandOptionExistsDataProvider(): array
	{
		$settings = new SettingsArray([
			'commands' => [
				'first:command' => null,
				'second:command' => ['options'],
				'third:command' => ['options' => null],
				'fourth:command' => ['options' => ['' => null]],
				'fourth:command' => ['options' => [null]],
				'fifth:command' => ['options' => [0 => null]],
				'sixth:command' => ['options' => ['opt1']],
				'seventh:command' => ['options' => ['opt1' => null]],
				'eigth:command' => ['options' => ['opt1' => '']],
				'ninth:command' => ['options' => ['opt1' => []]],
			],
		]);

		return [
			[$settings, 'first:command', 'opt1', false],
			[$settings, 'second:command', 'opt1', false],
			[$settings, 'third:command', 'opt1', false],
			[$settings, 'fourth:command', '', false],
			[$settings, 'fifth:command', 'opt1', false],
			[$settings, 'sixth:command', 'opt1', false],
			[$settings, 'seventh:command', 'opt1', true],
			[$settings, 'eigth:command', 'opt1', true],
			[$settings, 'ninth:command', 'opt1', true],
		];
	}

	// getCommandOptionDescription tests

	

	// /**
	//  * @test
	//  */
	// public function testIfGetSyntaxReturnsCorrectNullWhenIsMissing()
	// {
	// 	$configDTO = new ConfigDTOImpl([]);
	// 	$syntax = $configDTO->getSyntax();

	// 	$this->assertTrue($syntax === null);
	// }

	// /**
	//  * @test
	//  */
	// public function testIfGetSyntaxReturnsCorrectNullWhenNotString()
	// {
	// 	$configDTO = new ConfigDTOImpl(['description' => 1]);
	// 	$syntax = $configDTO->getSyntax();

	// 	$this->assertTrue($syntax === null);
	// }

	// /**
	//  * @test
	//  */
	// public function testIfGetGenericOptionsReturnsCorrectResultWhenTheyExist()
	// {
	// 	$this->instantiateConfigDTOWithValidData();
	// 	$options = $this->configDTO->getGenericOptions();

	// 	$this->assertEquals($options, [
	// 		'--first' => null,
	// 		'--second' => [],
	// 		'--some-option' => 'details',
	// 	]);
	// }

	// /**
	//  * @test
	//  */
	// public function testIfGetGenericOptionsReturnsEmptyArrayWhenTheyAreMissing()
	// {
	// 	$configDTO = new ConfigDTOImpl([]);
	// 	$options = $configDTO->getGenericOptions();

	// 	$this->assertTrue($options === []);
	// }

	// /**
	//  * @test
	//  */
	// public function testIfGetCommandDetailsReturnsDetailsWhenCommandExists()
	// {

	// 	$this->instantiateConfigDTOWithValidData();
	// 	$details = $this->configDTO->getCommandDetails('third-command');

	// 	$this->assertEquals($details, [
	// 		'syntax' => 1,
	// 		'description' => null,
	// 	]);
	// }

	// /**
	//  * @test
	//  */
	// public function testIfGetCommandDetailsReturnsWhenMissing()
	// {
	// 	$configDTO = new ConfigDTOImpl([]);
	// 	$details = $configDTO->getCommandDetails('missing');

	// 	$this->assertTrue($details === null);
	// }

	// /**
	//  * @test
	//  */
	// public function testIfGetCommandDetailsReturnsEmptyArrayWhenMissing()
	// {
	// 	$configDTO = new ConfigDTOImpl([
	// 		'commands' => [
	// 			'command' => null,
	// 		]
	// 	]);
	// 	$details = $configDTO->getCommandDetails('command');

	// 	$this->assertTrue($details === null);
	// }

	// /**
	//  * @test
	//  */
	// public function testIfGetCommandDetailsReturnsEmptyArrayWhenValueOfDetailsIsEmptyArray()
	// {
	// 	$configDTO = new ConfigDTOImpl([
	// 		'commands' => [
	// 			'command' => [],
	// 		]
	// 	]);
	// 	$details = $configDTO->getCommandDetails('command');

	// 	$this->assertTrue($details === []);
	// }

	// /**
	//  * @test
	//  */
	// public function testIfGetCommandDetailsReturnsEmptyNullWhenValueOfDetailsIsNotArray()
	// {
	// 	$configDTO = new ConfigDTOImpl([
	// 		'commands' => [
	// 			'command' => 'something',
	// 		]
	// 	]);
	// 	$details = $configDTO->getCommandDetails('command');

	// 	$this->assertTrue($details === null);
	// }

	// /**
	//  * @test
	//  */
	// public function testIfGetSpecificOptionsForCommandReturnsCorrectResultWhenTheyExistAndAreArray()
	// {
	// 	$this->instantiateConfigDTOWithValidData();
	// 	$options = $this->configDTO->getSpecificOptionsForCommand('example:command');

	// 	$this->assertEquals($options, [
	// 		'--third' => null,
	// 		'--fourth' => [
	// 			'descripttion' => null,
	// 			'args' => null,
	// 		],
	// 		'--fifth' => [
	// 			'decsription' => 'some desc',
	// 			'args' => [
	// 				'optArg1' => null,
	// 				'optArg2' => null,
	// 			],
	// 		]
	// 	]);
	// }

	// /**
	//  * @test
	//  */
	// public function testIfGetSpecificOptionsForCommandReturnsEmptyArrayWhenTheyAreMissing()
	// {
	// 	$this->instantiateConfigDTOWithValidData();
	// 	$options = $this->configDTO->getSpecificOptionsForCommand('fourth:command');

	// 	$this->assertTrue($options === []);
	// }

	// /**
	//  * @test
	//  */
	// public function testIfGetSpecificOptionsForCommandReturnsEmptyArrayWhenTheyAreNotArray()
	// {
	// 	$configDTO = new ConfigDTOImpl([
	// 		'commands' => [
	// 			'comm' => [
	// 				'options' => 'invalid type',
	// 			]
	// 		]
	// 	]);
	// 	$options = $configDTO->getSpecificOptionsForCommand('fourth:command');

	// 	$this->assertTrue($options === []);
	// }

	// /**
	//  * @test
	//  */
	// public function testIfGetOptionsForCommandReturnsCorrectResultWhenTheyExist()
	// {
	// 	$this->instantiateConfigDTOWithValidData();
	// 	$options = $this->configDTO->getOptionsForCommand('example:command');

	// 	$this->assertEquals($options, [
	// 		'--first' => null,
	// 		'--second' => [],
	// 		'--third' => null,
	// 		'--some-option' => 'details',
	// 		'--fourth' => [
	// 			'descripttion' => null,
	// 			'args' => null,
	// 		],
	// 		'--fifth' => [
	// 			'decsription' => 'some desc',
	// 			'args' => [
	// 				'optArg1' => null,
	// 				'optArg2' => null,
	// 			],
	// 		]
	// 	]);
	// }

	// /**
	//  * @test
	//  */
	// public function testIfGetOptionsForCommandHasNoReturnsEmptyArrayWhenAllOptionsAreMissing()
	// {
	// 	$configDTO = new ConfigDTOImpl([]);
	// 	$options = $configDTO->getOptionsForCommand('fourth:command');

	// 	$this->assertTrue($options === []);
	// }

	// /**
	//  * @test
	//  */
	// public function testIfGetOptionsForCommandReturnsOnlyGenericOptionsWhenCommandIsMissing()
	// {
	// 	$this->instantiateConfigDTOWithValidData();
	// 	$options = $this->configDTO->getOptionsForCommand('fourth:command');

	// 	$this->assertTrue($options === [
	// 		'--first' => null,
	// 		'--second' => [],
	// 		'--some-option' => 'details',
	// 	]);
	// }

	// /**
	//  * @test
	//  * @dataProvider dataForIsGenericOptionAvailable
	//  */
	// public function testIfIsGenericOptionAvailableReturnsCorrectResult(
	// 	string $option,
	// 	bool $expected
	// )
	// {
	// 	$this->instantiateConfigDTOWithValidData();
	// 	$result = $this->configDTO->isGenericOptionAvailable($option);

	// 	$this->assertEquals($expected, $result);
	// }

	// public function dataForIsGenericOptionAvailable()
	// {
	// 	return [
	// 		['missing', false],
	// 		['--first', true],
	// 	];
	// }

	// /**
	//  * @test
	//  * @dataProvider dataForGetDescriptionForCommand
	//  */
	// public function testIfGetDescriptionForCommandReturnsCorrectResult(
	// 	string $command,
	// 	?string $expected
	// )
	// {
	// 	$this->instantiateConfigDTOWithValidData();
	// 	$result = $this->configDTO->getDescriptionForCommand($command);

	// 	// if ($command == 'third-command')
	// 		// die(PHP_EOL.$result);

	// 	$this->assertEquals($expected, $result);
	// }

	// public function dataForGetDescriptionForCommand()
	// {
	// 	return [
	// 		['example:command', 'example command description'],
	// 		['missing:command', null],
	// 		['next:example', null],
	// 		['third-command', null],
	// 		['fourth:command', null],
	// 	];
	// }

	// /**
	//  * @test
	//  */
	// public function testIfGetGenericOptionDetailsReturnsCorrectResultWhenOptionExists()
	// {
	// 	$this->instantiateConfigDTOWithValidData();
	// 		$result = $this->configDTO->getGenericOptionDetails('--second');

	// 	$this->assertTrue($result === []);
	// }

	// /**
	//  * @test
	//  */
	// public function testIfGetGenericOptionDetailsReturnsNullWhenOptionMissing()
	// {
	// 	$this->instantiateConfigDTOWithValidData();
	// 	$result = $this->configDTO->getGenericOptionDetails('--sixth');

	// 	$this->assertTrue($result === null);
	// }

	// /**
	//  * @test
	//  */
	// public function testIfGetGenericOptionDetailsReturnsNullWhenDetailsAreNotArray()
	// {
	// 	$this->instantiateConfigDTOWithValidData();
	// 	$result = $this->configDTO->getGenericOptionDetails('--some-option');

	// 	$this->assertTrue($result === null);
	// }

	// /**
	//  * @test
	//  * @dataProvider dataForIsOptionAvailableForCommand
	//  */
	// public function testIfIsOptionAvailableForCommandReturnsCorrectValue(
	// 	string $option,
	// 	string $command,
	// 	mixed $expected
	// )
	// {
	// 	$this->instantiateConfigDTOWithValidData();
	// 	$result = $this->configDTO->isOptionAvailableForCommand($option, $command);

	// 	$this->assertTrue($result === $expected);
	// }

	// public function dataForIsOptionAvailableForCommand(): array
	// {
	// 	return [
	// 		['--first', 'example:command', true],
	// 		['--first', 'missing', false],
	// 		['--third', 'next:example', false],
	// 		['--third', 'example:command', true],
	// 	];
	// }

	// /**
	//  * @test
	//  */
	// public function testIfGetOptionDetailsForCommandReturnsCorrectResultIfOptionExists()
	// {
	// 	$this->instantiateConfigDTOWithValidData();
	// 	$result = $this->configDTO->getOptionDetailsForCommand('--fourth', 'example:command');

	// 	$this->assertTrue($result === [
	// 		'descripttion' => null,
	// 		'args' => null,
	// 	]);
	// }

	// /**
	//  * @test
	//  */
	// public function testIfGetOptionDetailsForCommandReturnsCorrectResultNullIfOptionDoesNotExistForCommand()
	// {
	// 	$this->instantiateConfigDTOWithValidData();
	// 	$result = $this->configDTO->getOptionDetailsForCommand('--sixth', 'example:command');

	// 	$this->assertTrue($result === null);
	// }
	
	// /**
	//  * @test
	//  */
	// public function testIfGetOptionDetailsForCommandReturnsNullIfCommandIsMissing()
	// {
	// 	$this->instantiateConfigDTOWithValidData();
	// 	$result = $this->configDTO->getOptionDetailsForCommand('--first', 'some-missing-command');

	// 	$this->assertTrue($result === null);
	// }
	
	// /**
	//  * @test
	//  */
	// public function testIfGetOptionDetailsForCommandReturnsNullIfOptionIsMissing()
	// {
	// 	$this->instantiateConfigDTOWithValidData();
	// 	$result = $this->configDTO->getOptionDetailsForCommand('--sixth', 'example:command');

	// 	$this->assertTrue($result === null);
	// }
	
	// /**
	//  * @test
	//  */
	// public function testIfGetOptionDetailsForCommandReturnsNullIfDetailAreNotArray()
	// {
	// 	$this->instantiateConfigDTOWithValidData();
	// 	$result = $this->configDTO->getOptionDetailsForCommand('--some-option', 'example:command');

	// 	$this->assertTrue($result === null);
	// }
	
	// /**
	//  * @test
	//  */
	// public function testIfGetArgsForOptionForCommandReturnsEmptyArrayWhenDetailsMissing()
	// {
	// 	$this->instantiateConfigDTOWithValidData();
	// 	$result = $this->configDTO->getArgsForOptionForCommand('--third', 'example:command');

	// 	$this->assertTrue($result === []);
	// }
	
	// /**
	//  * @test
	//  */
	// public function testIfGetArgsForOptionForCommandReturnsEmptyArrayWhenOptionIsMissing()
	// {
	// 	$this->instantiateConfigDTOWithValidData();
	// 	$result = $this->configDTO->getArgsForOptionForCommand('--third', 'next:example');

	// 	$this->assertTrue($result === []);
	// }
	
	// /**
	//  * @test
	//  */
	// public function testIfGetArgsForOptionForCommandReturnsEmptyArrayWhenCommandIsMissing()
	// {
	// 	$this->instantiateConfigDTOWithValidData();
	// 	$result = $this->configDTO->getArgsForOptionForCommand('--third', 'missing-command');

	// 	$this->assertTrue($result === []);
	// }
	
	// /**
	//  * @test
	//  */
	// public function testIfGetArgsForOptionForCommandReturnsCorrectResultWhenNothingIsMissing()
	// {
	// 	$this->instantiateConfigDTOWithValidData();
	// 	$result = $this->configDTO->getArgsForOptionForCommand('--fifth', 'example:command');

	// 	$this->assertTrue($result === [
	// 		'optArg1' => null,
	// 		'optArg2' => null,
	// 	]);
	// }
	
	// /**
	//  * @test
	//  */
	// public function testIfGetArgsForOptionForCommandReturnsCorrectResultIfBothExist()
	// {
	// 	$this->instantiateConfigDTOWithValidData();
	// 	$result = $this->configDTO->getOptionDetailsForCommand('--third', 'example:command');

	// 	$this->assertTrue($result === null);
	// }

	// [
	// 		'syntax' => '[options] [commands]',
	// 		'options' => [
	// 			'--first' => null,
	// 			'--second' => [],
	// 			'--some-option' => 'details',
	// 		],
	// 		'commands' => [
	// 			'example:command' => [
	// 				'syntax' => '[self] [arg1 ,[arg2]]',
	// 				'description' => 'example command description',
	// 				'options' => [
	// 					'--third' => null,
	// 					'--fourth' => [
	// 						'descripttion' => null,
	// 						'args' => null,
	// 					],
	// 					'--fifth' => [
	// 						'decsription' => 'some desc',
	// 						'args' => [
	// 							'optArg1' => null,
	// 							'optArg2' => null,
	// 						],
	// 					],
	// 				],
	// 				'args' => [
	// 					'commArgName',
	// 					'commArgValue',
	// 				],
	// 			],
	// 			'next:example' => [
	// 				'syntax' => '[self]',
	// 				'options' => null,
	// 				'args' => null,
	// 			],
	// 			'third-command' => [
	// 				'syntax' => 1,
	// 				'description' => null,
	// 			],
	// 			'fourth:command' => [
	// 				'description' => 1,
	// 			],
	// 		],
	// 	];
}