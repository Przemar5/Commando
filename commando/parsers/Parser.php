<?php

namespace Commando\Parsers;

use Commando\Models\Settings;

abstract class Parser
{
	protected Settings $settings;


	public function __construct(Settings $settings)
	{
		$this->settings = $settings;
	}

	abstract public function parseTokens(array $tokens): void;
}