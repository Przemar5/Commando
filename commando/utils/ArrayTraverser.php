<?php

namespace Commando\Utils;

require_once './commando/utils/Traverser.php';

use Commando\Utils\Traverser;

class ArrayTraverser implements Traverser
{
	public array $data;

	public function nestedKeyChainExists(array $chain): bool
	{
		return $this->checkIfExistsChainForData($chain, $this->data);
	}

	protected function checkIfExistsChainForData(array $chain, mixed $data): bool
	{
		if (empty($chain)) {
			return true;
		}
		elseif (is_array($data) && array_key_exists($chain[0], $data) && 
			(is_string($chain[0]) || is_int($chain[0]))) {
			
			return $this->checkIfExistsChainForData(
				array_slice($chain, 1),
				$data[$chain[0]]
			);
		}
		else {
			return false;
		}
	}

	public function getValueByNestedKeyChain(array $chain): mixed
	{
		if ($this->nestedKeyChainExists($chain, $this->data)) {
			return $this->traverseByKeyChain($chain, $this->data);
		}
		else {
			return null;
		}
	}

	protected function traverseByKeyChain(array $chain, mixed $data): mixed
	{
		if (empty($chain)) {
			return $data;
		}
		elseif (is_string($chain[0]) || is_int($chain[0])) {
			return $this->traverseByKeyChain(
				array_slice($chain, 1),
				$data[$chain[0]]
			);
		}
		else {
			return null;
		}
	}

	public function mergeValuesForMultipleKeyChains(array $chains): array
	{
		$first = [];

		if (isset($chains[0]) && is_array($chains[0]) && 
			$this->nestedKeyChainExists($chains[0])) {
			$first = [$this->getValueByNestedKeyChain($chains[0])];
		}

		if (isset($chains[1])) {
			return array_merge(
				$first, 
				$this->mergeValuesForMultipleKeyChains(array_slice($chains, 1))
			);
		}
		else {
			return $first;
		}
	}

	public function getValueIfKeyChainExists(array $chain)
	{
		if ($this->nestedKeyChainExists($chain))
			return $this->getValueByNestedKeyChain($chain);
	}
}