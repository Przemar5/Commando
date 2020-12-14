<?php

// namespace Tests\Commando\Framework;

require_once './commando/framework/ConfigDTOImpl.php';
require_once './commando/libs/ArrayTraverser.php';

use \Commando\Framework\ConfigDTOImpl;
use \Commando\Libs\ArrayTraverser;

class ConfigDTOImplTest extends PHPUnit\Framework\TestCase
{
	public array $data;
	public ConfigDTOImpl $configDTO;

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

	public function instantiateConfigDTOWithValidData()
	{
		$this->configDTO = new ConfigDTOImpl(
			$this->validConstructorData1()
		);
	}

	/**@test */
	public function testIfIsInstantiable()
	{
		$this->instantiateConfigDTOWithValidData();

		$this->assertIsObject($this->configDTO);
	}

	/**
	 * @test
	 * @dataProvider validDataForIsCommandAvailable
	 */
	public function testIfIsCommandAvailableReturnsTrueIfIs($configDTO, string $command)
	{
		$this->assertTrue($configDTO->isCommandAvailable($command));
	}

	public function validDataForIsCommandAvailable()
	{
		$config = new ConfigDTOImpl(
			$this->validConstructorData1()
		);

		return [
			[$config, 'example:command'],
			[$config, 'next:example'],
		];
	}

	public function invalidDataForIsCommandAvailable()
	{
		$config = new ConfigDTOImpl(
			$this->validConstructorData1()
		);

		return [
			[$config, 'missing-command'],
			[$config, 'next:missing'],
		];
	}

	/**
	 * @test
	 * @dataProvider invalidDataForIsCommandAvailable
	 */
	public function testIfIsCommandAvailableReturnsFalseIfNot($configDTO, string $command)
	{
		$this->assertFalse($configDTO->isCommandAvailable($command));
	}

	/**
	 * @test
	 */
	public function testIfGetSyntaxReturnsCorrectResultWhenExists()
	{
		$this->instantiateConfigDTOWithValidData();
		$syntax = $this->configDTO->getSyntax();

		$this->assertEquals($syntax, '[options] [commands]');
	}

	/**
	 * @test
	 */
	public function testIfGetSyntaxReturnsCorrectNullWhenIsMissing()
	{
		$configDTO = new ConfigDTOImpl([]);
		$syntax = $configDTO->getSyntax();

		$this->assertTrue($syntax === null);
	}

	/**
	 * @test
	 */
	public function testIfGetSyntaxReturnsCorrectNullWhenNotString()
	{
		$configDTO = new ConfigDTOImpl(['description' => 1]);
		$syntax = $configDTO->getSyntax();

		$this->assertTrue($syntax === null);
	}

	/**
	 * @test
	 */
	public function testIfGetGenericOptionsReturnsCorrectResultWhenTheyExist()
	{
		$this->instantiateConfigDTOWithValidData();
		$options = $this->configDTO->getGenericOptions();

		$this->assertEquals($options, [
			'--first' => null,
			'--second' => [],
		]);
	}

	/**
	 * @test
	 */
	public function testIfGetGenericOptionsReturnsEmptyArrayWhenTheyAreMissing()
	{
		$configDTO = new ConfigDTOImpl([]);
		$options = $configDTO->getGenericOptions();

		$this->assertTrue($options === []);
	}

	/**
	 * @test
	 */
	public function testIfGetCommandDetailsReturnsDetailsWhenCommandExists()
	{

		$this->instantiateConfigDTOWithValidData();
		$details = $this->configDTO->getCommandDetails('third-command');

		$this->assertEquals($details, [
			'syntax' => 1,
			'description' => null,
		]);
	}

	/**
	 * @test
	 */
	public function testIfGetCommandDetailsReturnsWhenMissing()
	{
		$configDTO = new ConfigDTOImpl([]);
		$details = $configDTO->getCommandDetails('missing');

		$this->assertTrue($details === null);
	}

	/**
	 * @test
	 */
	public function testIfGetCommandDetailsReturnsEmptyArrayWhenMissing()
	{
		$configDTO = new ConfigDTOImpl([
			'commands' => [
				'command' => null,
			]
		]);
		$details = $configDTO->getCommandDetails('command');

		$this->assertTrue($details === null);
	}

	/**
	 * @test
	 */
	public function testIfGetCommandDetailsReturnsEmptyArrayWhenValueOfDetailsIsEmptyArray()
	{
		$configDTO = new ConfigDTOImpl([
			'commands' => [
				'command' => [],
			]
		]);
		$details = $configDTO->getCommandDetails('command');

		$this->assertTrue($details === []);
	}

	/**
	 * @test
	 */
	public function testIfGetCommandDetailsReturnsEmptyNullWhenValueOfDetailsIsNotArray()
	{
		$configDTO = new ConfigDTOImpl([
			'commands' => [
				'command' => 'something',
			]
		]);
		$details = $configDTO->getCommandDetails('command');

		$this->assertTrue($details === null);
	}

	/**
	 * @test
	 */
	public function testIfGetSpecificOptionsForCommandReturnsCorrectResultWhenTheyExistAndAreArray()
	{
		$this->instantiateConfigDTOWithValidData();
		$options = $this->configDTO->getSpecificOptionsForCommand('example:command');

		$this->assertEquals($options, [
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
			]
		]);
	}

	/**
	 * @test
	 */
	public function testIfGetSpecificOptionsForCommandReturnsEmptyArrayWhenTheyAreMissing()
	{
		$this->instantiateConfigDTOWithValidData();
		$options = $this->configDTO->getSpecificOptionsForCommand('fourth:command');

		$this->assertTrue($options === []);
	}

	/**
	 * @test
	 */
	public function testIfGetSpecificOptionsForCommandReturnsEmptyArrayWhenTheyAreNotArray()
	{
		$configDTO = new ConfigDTOImpl([
			'commands' => [
				'comm' => [
					'options' => 'invalid type',
				]
			]
		]);
		$options = $configDTO->getSpecificOptionsForCommand('fourth:command');

		$this->assertTrue($options === []);
	}

	/**
	 * @test
	 */
	public function testIfGetOptionsForCommandReturnsCorrectResultWhenTheyExist()
	{
		$this->instantiateConfigDTOWithValidData();
		$options = $this->configDTO->getOptionsForCommand('example:command');

		$this->assertEquals($options, [
			'--first' => null,
			'--second' => [],
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
			]
		]);
	}

	/**
	 * @test
	 */
	public function testIfGetOptionsForCommandHasNoReturnsEmptyArrayWhenAllOptionsAreMissing()
	{
		$configDTO = new ConfigDTOImpl([]);
		$options = $configDTO->getOptionsForCommand('fourth:command');

		$this->assertTrue($options === []);
	}

	/**
	 * @test
	 */
	public function testIfGetOptionsForCommandReturnsOnlyGenericOptionsWhenCommandIsMissing()
	{
		$this->instantiateConfigDTOWithValidData();
		$options = $this->configDTO->getOptionsForCommand('fourth:command');

		$this->assertTrue($options === [
			'--first' => null,
			'--second' => [],
		]);
	}

	/**
	 * @test
	 * @dataProvider dataForIsGenericOptionAvailable
	 */
	public function testIfIsGenericOptionAvailableReturnsCorrectResult(
		string $option,
		bool $expected
	)
	{
		$this->instantiateConfigDTOWithValidData();
		$result = $this->configDTO->isGenericOptionAvailable($option);

		$this->assertEquals($expected, $result);
	}

	public function dataForIsGenericOptionAvailable()
	{
		return [
			['missing', false],
			['--first', true],
		];
	}

	/**
	 * @test
	 * @dataProvider dataForGetDescriptionForCommand
	 */
	public function testIfGetDescriptionForCommandReturnsCorrectResult(
		string $command,
		?string $expected
	)
	{
		$this->instantiateConfigDTOWithValidData();
		$result = $this->configDTO->getDescriptionForCommand($command);

		// if ($command == 'third-command')
			// die(PHP_EOL.$result);

		$this->assertEquals($expected, $result);
	}

	public function dataForGetDescriptionForCommand()
	{
		return [
			['example:command', 'example command description'],
			['missing:command', null],
			['next:example', null],
			['third-command', null],
			['fourth:command', null],
		];
	}

	// /**
	//  * @test
	//  */
	// public function testIfGetSpecificOptionsForCommandHasNoReturnWhenTheyAreMissing()
	// {
	// 	$this->instantiateConfigDTOWithValidData();
	// 	$options = $this->configDTO->getSpecificOptionsForCommand('fourth:command');

	// 	$this->assertTrue(!isset($options));
	// }

	// /**
	//  * @test
	//  * @dataProvider invalidDataForIsCommandAvailable
	//  */
	// public function testIfGetOptionsForCommandOrThrowExceptionThrowsExceptionIfCommandMissing($configDTO, string $command)
	// {
	// 	$this->expectException(\Exception::class);
	// 	$configDTO->getOptionsForCommandOrThrowException($command);
	// }

	// public function dataForGetOptionsForCommandOrThrowException()
	// {
	// 	$config = new ConfigDTOImpl(
	// 		$this->validConstructorData1(),
	// 		new ArrayTraverser()
	// 	);

	// 	return [
	// 		[
	// 			$config,
	// 			'example:command',
	// 			['--first', '--second', '--third', '--fourth', '--fifth'],
	// 		],
	// 	];
	// }

	// /**
	//  * @test
	//  * @dataProvider dataForGetOptionsForCommandOrThrowException
	//  */
	// public function testIfGetOptionsForCommandOrThrowExceptionReturnsAllOptions(
	// 	$configDTO, 
	// 	string $command, 
	// 	array $expected
	// )
	// {
	// 	$result = $configDTO->getOptionsForCommandOrThrowException($command);

	// 	$this->assertTrue($result === $expected);
	// }

	// public function dataForIsOptionAvailableForCommand()
	// {
	// 	$config = new ConfigDTOImpl(
	// 		$this->validConstructorData1(),
	// 		new ArrayTraverser()
	// 	);

	// 	return [
	// 		[
	// 			$config,
	// 			'--first',
	// 			'example:command',
	// 			true,
	// 		],
	// 		[
	// 			$config,
	// 			'--third',
	// 			'example:command',
	// 			true,
	// 		],
	// 		[
	// 			$config,
	// 			'--sixth',
	// 			'example:command',
	// 			false,
	// 		],
	// 		[
	// 			$config,
	// 			'--second',
	// 			'next:example',
	// 			true,
	// 		],
	// 	];
	// }

	// /**
	//  * @test
	//  * @dataProvider dataForIsOptionAvailableForCommand
	//  */
	// public function testIfIsOptionAvailableForCommandWorks(
	// 	$configDTO,
	// 	string $option,
	// 	string $command,
	// 	bool $expected
	// )
	// {
	// 	$result = $configDTO->isOptionAvailableForCommand($option, $command);

	// 	$this->assertTrue($result === $expected);
	// }

	// public function dataForgetSyntaxForCommandOrThrowException()
	// {
	// 	$config = new ConfigDTOImpl(
	// 		$this->validConstructorData1(),
	// 		new ArrayTraverser()
	// 	);

	// 	return [
	// 		[
	// 			$config,
	// 			'example:command',
	// 			'[self] [arg1 ,[arg2]]',
	// 		],
	// 		[
	// 			$config,
	// 			'next:example',
	// 			'[self]',
	// 		],
	// 	];
	// }

	// /**
	//  * @test
	//  * @dataProvider dataForgetSyntaxForCommandOrThrowException
	//  */
	// public function testIfgetSyntaxForCommandOrThrowExceptionReturnsCorrectResult(
	// 	$configDTO,
	// 	string $command,
	// 	string $expected
	// )
	// {
	// 	$result = $configDTO->getSyntaxForCommandOrThrowException($command);

	// 	$this->assertEquals($result, $expected);
	// }

	// public function dataForgetSyntaxForCommandOrThrowExceptionForThrowing()
	// {
	// 	$config = new ConfigDTOImpl(
	// 		$this->validConstructorData1(),
	// 		new ArrayTraverser()
	// 	);

	// 	return [
	// 		[
	// 			$config,
	// 			'third-command',
	// 		],
	// 		[
	// 			$config,
	// 			'fourth:command',
	// 		],
	// 	];
	// }

	// /**
	//  * @test
	//  * @dataProvider dataForgetSyntaxForCommandOrThrowExceptionForThrowing
	//  */
	// public function testIfGetSyntaxForCommandOrThrowExceptionThrowsException(
	// 	$configDTO,
	// 	string $command
	// )
	// {
	// 	$this->expectException(\Exception::class);
	// 	$configDTO->getSyntaxForCommandOrThrowException($command);
	// }

	// public function validDataForGetArgsOfOptionForCommand()
	// {
	// 	$config = new ConfigDTOImpl($this->validConstructorData1());

	// 	return [
	// 		[
	// 			$config,
	// 			'example:command',
	// 			'--first',
	// 			[],
	// 		],
	// 		[
	// 			$config,
	// 			'example:command',
	// 			'--second',
	// 			[],
	// 		],
	// 	];
	// }

	// /**
	//  * @test
	//  * @dataProvider validDataForGetArgsOfOptionForCommand
	//  */
	// public function testIfGetArgsOfOptionForCommandReturnArgNames(
	// 	$configDTO,
	// 	string $command,
	// 	string $option,
	// 	array $expected
	// )
	// {
	// 	$result = $configDTO->getArgsOfOptionForCommand($option, $command);

	// 	$this->assertTrue($result === $expected);
	// }
}