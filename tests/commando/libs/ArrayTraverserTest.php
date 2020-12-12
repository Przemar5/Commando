<?php

require_once './commando/libs/Traverser.php';
require_once './commando/libs/ArrayTraverser.php';

use \Commando\Libs\ArrayTraverser;

class ArrayTraverserTest extends PHPUnit\Framework\TestCase
{
	private ArrayTraverser $traverser;

	public function setUp(): void
	{
		$this->traverser = new ArrayTraverser();
	}

	/**@test */
	public function testIfWorks()
	{
		$this->assertTrue(true);
	}

	/**@test */
	public function testIfIsInstantiable()
	{
		$this->assertIsObject($this->traverser);
	}

	/**
	 * @test
	 * @dataProvider nestedKeyChainExistsDataProvider
	 */
	public function testIfNestedKeyChainExistsReturnsCorrectResult(
		array $chain,
		bool $expected
	)
	{
		$this->injectData1();
		$result = $this->traverser->nestedKeyChainExists($chain);

		$this->assertIsObject($this->traverser);
	}

	private function injectData1()
	{
		$data = [
			'base_1' => null,
			'base_2' => 2,
			'base_3' => 0,
			'base_4' => 'string',
			'base_5' => [],
			'base_6' => [null],
			'base_7' => [
				'layer_1_1' => null,
				'layer_1_2' => [
					[],
					'layer_2_1' => null,
					'layer_2_2' => 'layer_2_2_content',
				],
			],
		];

		$this->traverser->data = $data;
	}

	public function nestedKeyChainExistsDataProvider(): array
	{
		return [
			[['base_1'], true],
			[['base_5'], false],
			[['base_6', 0], true],
			[['base_7', 'layer_1_1'], true],
			[['base_7', 'layer_1_2'], true],
			[['base_7', 'layer_1_2', 'layer_2_2', 0], false],
		];
	}

	/**
	 * @test
	 * @dataProvider getValueByNestedKeyChainDataProvider
	 */
	public function testIfGetValueByNestedKeyChainReturnsCorrectResult(
		array $chain,
		mixed $expected
	)
	{
		$this->injectData1();
		$result = $this->traverser->getValueByNestedKeyChain($chain);

		$this->assertTrue($result === $expected);
	}

	public function getValueByNestedKeyChainDataProvider(): array
	{
		return [
			[['base_1'], null],
			[['base_6', 0], null],
			[['base_7', 'layer_1_1'], null],
			[['base_7', 'layer_1_2'], [
					[],
					'layer_2_1' => null,
					'layer_2_2' => 'layer_2_2_content',
				]
			],
			[['base_7', 'layer_1_2', 'layer_2_2'], 'layer_2_2_content'],
			[['base_7', 'layer_1_2', 'layer_2_2', 0], null],
		];
	}

	/**
	 * @test
	 */
	public function testIfMergeValuesForMultipleKeyChainsReturnsArray()
	{
		$this->injectData1();
		$result = $this->traverser->mergeValuesForMultipleKeyChains([['base_1']]);

		$this->assertTrue(is_array($result));
	}

	/**
	 * @test
	 */
	public function testIfMergeValuesForMultipleKeyChainsReturnsResultForSingleIfPassedSingleChainInArray()
	{
		$this->injectData1();
		$result = $this->traverser->mergeValuesForMultipleKeyChains([['base_1']]);

		$this->assertTrue($result === [null]);
	}

	/**
	 * @test
	 */
	public function testIfMergeValuesForMultipleKeyChainsIgnoresNonExistingKeysWhenPassedSingleArray()
	{
		$this->injectData1();
		$result = $this->traverser->mergeValuesForMultipleKeyChains([['somethin']]);

		$this->assertTrue($result === []);
	}

	/**
	 * @test
	 */
	public function testIfMergeValuesForMultipleKeyChainsIgnoresNonArrayInArray()
	{
		$this->injectData1();
		$result = $this->traverser->mergeValuesForMultipleKeyChains(['something']);

		$this->assertTrue($result === []);
	}

	/**
	 * @test
	 */
	public function testIfMergeValuesForMultipleKeyChainsAcceptsMultipleChainsPassedAsArray()
	{
		$this->injectData1();
		$result = $this->traverser->mergeValuesForMultipleKeyChains(
			[['base_1'], ['base_3'], ['base_4']]
		);

		$this->assertTrue($result === [null, 0, 'string']);
	}

	/**
	 * @test
	 */
	public function testIfMergeValuesForMultipleKeyChainsIgnoresNonStringsWhenPassingMultipleChainsAsArray()
	{
		$this->injectData1();
		$result = $this->traverser->mergeValuesForMultipleKeyChains(
			[['base_1'], ['base_3'], 'some-item', ['base_4']]
		);

		$this->assertTrue($result === [null, 0, 'string']);
	}

	/**
	 * @test
	 */
	public function testIfMergeValuesForMultipleKeyChainsIgnoresNonExistingKeyChainsWhenPassingMultipleChainsAsArray()
	{
		$this->injectData1();
		$result = $this->traverser->mergeValuesForMultipleKeyChains(
			[['base_1'], ['missing'], ['base_3'], 
			['another_missing'], ['base_4'], ['base_7', 'layer_1_1']]
		);

		$this->assertTrue($result === [null, 0, 'string', null]);
	}
}