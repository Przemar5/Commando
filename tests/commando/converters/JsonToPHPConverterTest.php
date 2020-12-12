<?php

require_once './commando/converters/Converter.php';
require_once './commando/converters/JsonToPHPConverter.php';

use Commando\Converters\JsonToPHPConverter as JsonToPHPConverter;

class JsonToPHPConverterTest extends PHPUnit\Framework\TestCase
{
	private JsonToPHPConverter $converter;

	public function setUp(): void
	{
		$this->converter = new JsonToPHPConverter();
	}

	/**@test */
	public function testIfWorks()
	{
		$this->assertTrue(true);
	}

	/**@test */
	public function testIfIsInstantiable()
	{
		$this->assertIsObject($this->converter);
	}

	public function convertValidInputsAndOutputs()
	{
		return [
			[
				'{"test":1}',
				['test' => 1],
			],
			[
				'{"test1":1, "test2": 2}',
				[
					'test1' => 1,
					'test2' => 2,
				],
			],
			[
				'{
					"test": 1
				}',
				['test' => 1],
			],
			[
				'{
					"test":

					1
				}',
				['test' => 1],
			],
			[
				'{
					"test": []
				}',
				['test' => []],
			],
			[
				'{
					"test": {}
				}',
				['test' => []],
			],
			[
				'{
					"test": [
						1, 2, 3
					]
				}',
				['test' => [1, 2, 3]],
			],
			[
				'{
					"test": {
						"first": 1,
						"second": 2
					}
				}',
				['test' => [
					'first' => 1,
					'second' => 2,
				]],
			],
			[
				'{
					"test": {
						"first": [1, 2, 3],
						"second": 2
					}
				}',
				['test' => [
					'first' => [1, 2, 3],
					'second' => 2,
				]],
			],
		];
	}

	/**
	 * @test
	 * @dataProvider convertValidInputsAndOutputs
	 */
	public function testConvertReturnsArrayOnValidJsonInput(string $input, array $expected)
	{
		$output = $this->converter::convert($input);

		$this->assertTrue($output === $expected);
	}

	public function convertInvalidInputs()
	{
		return [
			['{"test": 1,}'],
			['{"test": 1},'],
			['{test: 1}'],
			['{\'test\': 1}'],
			['{"test": [1,]}'],
			['{"test": [1, "arg": 2]}'],
			['{"test": {1, "arg": 2}}'],
		];
	}

	/**
	 * @test
	 * @dataProvider convertInvalidInputs
	 */
	public function testConvertOutputsExceptionMessageOnInvalidJsonInput(string $input)
	{
		$expected = 'Json has invalid format.'.PHP_EOL;

		$this->expectOutputString($expected);
		$this->converter::convert($input);
	}
}