<?php

namespace Commando\Framework;

use Commando\Converters\Converter;
use Commando\Validators\Validator;

abstract class CommandLineParser
{
	private Validator $validator;
	private array $syntax;

	public function __construct(Validator $validator)
	{
		$this->validator = $validator;
	}

	public function setConfigOptions(array $configuration)
	{
		$this->syntax = $configuration['syntax'];
	}

	private function extractArgs()
	{
		//
	}
}