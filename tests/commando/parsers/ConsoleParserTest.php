<?php

require_once './commando/parsers/Parser.php';
require_once './commando/parsers/ConsoleParser.php';
require_once './commando/models/SettingsArray.php';

use \Commando\Parsers\Parser;
use \Commando\Parsers\ConsoleParser;
use \Commando\Models\SettingsArray;

class ConsoleParserTest extends PHPUnit\Framework\TestCase
{
	/**
	 * @test
	 */
	public function testIfWorks()
	{
		$this->assertTrue(true);
	}

	/**
	 * @test
	 * @dataProvider parseTokensDataProvider
	 */
	public function testIfParseTokensWorksCorrectly(
		array $tokens,
		ConsoleParser $parser,
		?string $expectedCommand,
		?array $expectedArgs,
		?array $expectedOptions,
		?string $expectedClass,
		?string $expectedError
	)
	{
		$parser->parseTokens($tokens);

		$this->assertEquals($parser->getCommand(), $expectedCommand);
		$this->assertTrue($parser->getArgs() === $expectedArgs);
		$this->assertTrue($parser->getOptions() === $expectedOptions);
		$this->assertEquals($parser->getCommandClass(), $expectedClass);
		$this->assertEquals($parser->getError(), $expectedError);
	}

	public function parseTokensDataProvider(): array
	{
		$settings = new SettingsArray([
			'syntax' => 'command',
			'options' => [
				'-first' => ['syntax' => '-first'],
				'-second' => ['syntax' => '-second Example'],
			],
			'commands' => [
				'first:command' => ['options' => ['-third' => []]],
				'second:command' => ['args' => ''],
				'third:command' => ['args' => 1],
				'fourth:command' => null,
				'fifth:command' => ['args' => ['arg1']],
				'sixth:command' => ['args' => ['arg1'], 'options' => ['-fourth' => ['args' => ['a', 'b']]]],
				'seventh:command' => ['class' => 'SeventhCommand'],
			],
		]);
		$parser = new ConsoleParser($settings);

		return [
			[[], $parser, null, [], [], null, 'You must specify command'],
			[[''], $parser, null, [], [], null, 'You must specify command'],
			[['command'], $parser, null, [], [], null, "Command 'command' does not exist"],
			[['first:command'], $parser, 'first:command', [], [], null, null],
			[['second:command'], $parser, 'second:command', [], [], null, null],
			[['third:command'], $parser, 'third:command', [], [], null, null],
			[['fourth:command'], $parser, 'fourth:command', [], [], null, null],
			[['fifth:command'], $parser, 'fifth:command', [], [], null, 
				"Command 'fifth:command' needs 1 more argument"],
			[['fifth:command', 'arg'], $parser, 'fifth:command', ['arg'], [], null, null],
			[['fifth:command', 'arg1', 'arg2'], $parser, 'fifth:command', 
				['arg1'], [], null, "All options must start with '-'"],
			[['fifth:command', 'arg1', '-missing-option'], $parser, 'fifth:command', ['arg1'], [], null, 
				"Option '-missing-option' is not available for command 'fifth:command'"],
			[['fifth:command', '-first'], $parser, 'fifth:command', [], [], null, 
				"Command arguments cannot start with '-'"],
			[['first:command', '-first'], $parser, 'first:command', 
				[], ['-first' => []], null, null],
			[['fifth:command', 'arg1', '-first'], $parser, 'fifth:command', 
				['arg1'], ['-first' => []], null, null],
			[['third:command', '-third'], $parser, 'third:command', [], [], null, 
				"Option '-third' is not available for command 'third:command'"],
			[['sixth:command', 'arg1', '-fourth'], $parser, 'sixth:command', 
				['arg1'], ['-fourth' => []], null, "Option '-fourth' needs 2 more arg"],
			[['sixth:command', 'arg', '-fourth', 'a', 'b'], $parser, 'sixth:command', ['arg'], 
				['-fourth' => ['a', 'b']], null, null],
			[['seventh:command'], $parser, 'seventh:command', [], [], 
				'SeventhCommand', null],
		];
	}
}