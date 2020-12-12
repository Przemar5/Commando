<?php

namespace Commando\Converters;

use Commando\Converters\Converter;
use Commando\Exceptions\InvalidFileFormatException;

class JsonToPHPConverter implements Converter
{
	public static function convert(string $content)
	{
		try {
			return self::jsonDecodeOrThrowException($content);
		}
		catch (\Exception $e) {
			echo $e->getMessage();
		}
	}

	private static function jsonDecodeOrThrowException(string $content)
	{
		$content = json_decode($content, true);

		if (is_null($content) || $content === false) {
			throw new \Exception('Json has invalid format.'.PHP_EOL);
		}

		return (array) $content;
	}
}