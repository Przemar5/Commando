<?php

namespace Commando\Validators;

require_once './commando/validators/Validator.php';

use Commando\Validators\Validator;

class ConsoleQuerySyntaxValidator implements Validator
{
	public array $syntax;

	public function validate($value): bool
	{
		return true;
	}
}
