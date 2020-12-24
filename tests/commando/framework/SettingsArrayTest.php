<?php

declare(strict_types = 1);
// namespace Tests\Commando\Framework;

require_once './commando/models/SettingsArray.php';
require_once './commando/utils/ArrayTraverser.php';

use \Commando\Models\SettingsArray;
use \Commando\Utils\ArrayTraverser;

class SettingsArrayTest extends PHPUnit\Framework\TestCase
{
	public array $data;
	public SettingsArray $settings;
	

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
					'args' => ['arg1', 'arg2'],
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
					'args' => [],
					'options' => null,
					'args' => null,
				],
				'third-command' => [
					'args' => 1,
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

	// commandExists tests

	/**
	 * @test
	 * @dataProvider commandExistsDataProvider
	 */
	public function testIfCommandExistsReturnsCorrectResult(
		$settings, 
		string $command,
		bool $expected
	)
	{
		$result = $settings->commandExists($command);

		$this->assertTrue($result === $expected);
	}

	public function commandExistsDataProvider()
	{
		$settings = new SettingsArray([
			'commands' => [
				'first:command' => null,
				'second:command' => [
					'fifth:command' => '',
				],
				'third:command',
			],
			'fourth:command' => null,
		]);

		return [
			[$settings, 'first:command', true],
			[$settings, 'next:example', false],
			[$settings, 'third:command', false],
			[$settings, 'fourth:command', false],
			[$settings, 'fifth:command', false],
		];
	}

	// getCommandArgs tests

	/**
	 * @test
	 * @dataProvider getCommandArgsDataProvider
	 */
	public function testIfGetCommandArgsReturnsCorrectResult(
		SettingsArray $settings,
		string $command,
		array $expected
	)
	{
		$result = $settings->getCommandArgs($command);

		$this->assertTrue($result === $expected);
	}

	public function getCommandArgsDataProvider(): array
	{
		$settings = new SettingsArray([
			'commands' => [
				'first:command' => null,
				'second:command' => ['args'],
				'third:command' => ['args' => null],
				'fourth:command' => ['args' => ''],
				'fifth:command' => ['args' => ['arg1', 'arg2']],
				'sixth:command' => ['args' => ['arg1' => 'a', 'arg2' => 'b']],
				'seventh:command' => ['args' => 45],
				'eigth:command' => ['args' => []],
			],
		]);

		return [
			[$settings, 'first:command', []],
			[$settings, 'second:command', []],
			[$settings, 'third:command', []],
			[$settings, 'fourth:command', []],
			[$settings, 'fifth:command', ['arg1', 'arg2']],
			[$settings, 'sixth:command', ['a', 'b']],
			[$settings, 'seventh:command', []],
			[$settings, 'eigth:command', []],
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

	// getCommandClass tests

	/**
	 * @test
	 * @dataProvider getCommandClassDataProvider
	 */
	public function testIfGetCommandClassReturnsCorrectResult(
		SettingsArray $settings,
		string $command,
		?string $expected
	)
	{
		$class = $settings->getCommandClass($command);

		$this->assertEquals($class, $expected);
	}

	public function getCommandClassDataProvider(): array
	{
		$settings = new SettingsArray([
			'commands' => [
				'first:command' => null,
				'second:command' => ['class' => 'ExampleCommand'],
			],
		]);

		return [
			[$settings, 'first:command', null],
			[$settings, 'second:command', 'ExampleCommand'],
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
			'options' => [
				'opt2',
				'opt3' => null,
				'opt4' => '',
			],
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
			[$settings, 'ninth:command', 'opt2', false],
			[$settings, 'ninth:command', 'opt3', true],
			[$settings, 'ninth:command', 'opt4', true],
			[$settings, 'missing:command', 'opt4', false],
		];
	}

	// getCommandOptionDescription tests

	/**
	 * @test
	 * @dataProvider getCommandOptionDescriptionDataProvider
	 */
	public function testIfGetCommandOptionDescriptionReturnsCorrectResult(
		SettingsArray $settings,
		string $command,
		string $option,
		mixed $expected
	)
	{
		$result = $settings->getCommandOptionDescription($command, $option);

		$this->assertTrue($result === $expected);
	}

	public function getCommandOptionDescriptionDataProvider(): array
	{
		$settings = new SettingsArray([
			'options' => [
				'opt2',
				'opt3' => null,
				'opt4' => ['description'],
				'opt5' => ['description' => null],
				'opt6' => ['description' => 1],
				'opt7' => ['description' => ''],
				'opt8' => ['description' => 'desc'],
			],
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
				'ninth:command' => ['options' => ['opt1' => ['description']]],
				'tenth:command' => ['options' => ['opt1' => ['description' => null]]],
				'eleventh:command' => ['options' => ['opt1' => ['description' => 1]]],
				'twelfth:command' => ['options' => ['opt1' => ['description' => '']]],
				'thirtenth:command' => ['options' => ['opt1' => ['description' => 'desc']]],
			],
		]);

		return [
			[$settings, 'first:command', 'opt1', null],
			[$settings, 'second:command', 'opt1', null],
			[$settings, 'third:command', 'opt1', null],
			[$settings, 'fourth:command', '', null],
			[$settings, 'fifth:command', 'opt1', null],
			[$settings, 'sixth:command', 'opt1', null],
			[$settings, 'seventh:command', 'opt1', null],
			[$settings, 'eigth:command', 'opt1', null],
			[$settings, 'ninth:command', 'opt1', null],
			[$settings, 'tenth:command', 'opt1', null],
			[$settings, 'eleventh:command', 'opt1', null],
			[$settings, 'twelfth:command', 'opt1', ''],
			[$settings, 'thirtenth:command', 'opt1', 'desc'],
			[$settings, 'thirtenth:command', 'opt2', null],
			[$settings, 'thirtenth:command', 'opt3', null],
			[$settings, 'thirtenth:command', 'opt4', null],
			[$settings, 'thirtenth:command', 'opt5', null],
			[$settings, 'thirtenth:command', 'opt6', null],
			[$settings, 'thirtenth:command', 'opt7', ''],
			[$settings, 'thirtenth:command', 'opt8', 'desc'],
			[$settings, 'missing:command', 'opt8', null],
		];
	}

	// getCommandOptionArgs tests

	/**
	 * @test
	 * @dataProvider getCommandOptionArgsDataProvider
	 */
	public function testIfGetCommandOptionArgsReturnsCorrectResult(
		SettingsArray $settings,
		string $command,
		string $option,
		array $expected
	)
	{
		$result = $settings->getCommandOptionArgs($command, $option);

		$this->assertTrue($result === $expected);
	}

	public function getCommandOptionArgsDataProvider(): array
	{
		$settings = new SettingsArray([
			'options' => [
				'opt2',
				'opt3' => null,
				'opt4' => ['args'],
				'opt5' => ['args' => null],
				'opt6' => ['args' => 1],
				'opt7' => ['args' => []],
				'opt8' => ['args' => ''],
				'opt9' => ['args' => 'arg'],
				'opt10' => ['args' => ['arg1', 'arg2']],
				'opt11' => ['args' => ['a' => 'arg1', 'b' => 'arg2']],
				'opt12' => ['args' => ['a' => 1, 'b' => 2]],
			],
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
				'ninth:command' => ['options' => ['opt1' => ['args']]],
				'tenth:command' => ['options' => ['opt1' => ['args' => null]]],
				'eleventh:command' => ['options' => ['opt1' => ['args' => 1]]],
				'twelfth:command' => ['options' => ['opt1' => ['args' => '']]],
				'thirtenth:command' => ['options' => ['opt1' => ['args' => 'arg1']]],
				'fourtenth:command' => ['options' => ['opt1' => ['args' => ['arg1', 'arg2']]]],
				'fiftenth:command' => ['options' => ['opt1' => ['args' => ['a' => 'arg1', 'b' => 'arg2']]]],
				'sixtenth:command' => ['options' => ['opt1' => ['args' => ['a' => 1, 'b' => 2]]]],
			],
		]);

		return [
			[$settings, 'first:command', 'opt1', []],
			[$settings, 'second:command', 'opt1', []],
			[$settings, 'third:command', 'opt1', []],
			[$settings, 'fourth:command', '', []],
			[$settings, 'fifth:command', 'opt1', []],
			[$settings, 'sixth:command', 'opt1', []],
			[$settings, 'seventh:command', 'opt1', []],
			[$settings, 'eigth:command', 'opt1', []],
			[$settings, 'ninth:command', 'opt1', []],
			[$settings, 'tenth:command', 'opt1', []],
			[$settings, 'eleventh:command', 'opt1', []],
			[$settings, 'twelfth:command', 'opt1', []],
			[$settings, 'thirtenth:command', 'opt1', []],
			[$settings, 'fourtenth:command', 'opt1', ['arg1', 'arg2']],
			[$settings, 'fiftenth:command', 'opt1', ['arg1', 'arg2']],
			[$settings, 'sixtenth:command', 'opt1', []],
			[$settings, 'first:command', 'opt2', []],
			[$settings, 'first:command', 'opt3', []],
			[$settings, 'first:command', 'opt4', []],
			[$settings, 'first:command', 'opt5', []],
			[$settings, 'first:command', 'opt6', []],
			[$settings, 'first:command', 'opt7', []],
			[$settings, 'first:command', 'opt8', []],
			[$settings, 'first:command', 'opt9', []],
			[$settings, 'first:command', 'opt10', ['arg1', 'arg2']],
			[$settings, 'first:command', 'opt11', ['arg1', 'arg2']],
			[$settings, 'first:command', 'opt12', []],
		];
	}
}