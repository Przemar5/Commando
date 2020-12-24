<?php

namespace Commando\Converters;

interface Converter
{
	public static function convert(string $content);
}