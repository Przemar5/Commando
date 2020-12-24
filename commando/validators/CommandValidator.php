<?php

namespace Commando\Validators;

use Commando\Validators\Validator;

class CommandValidator implements Validator
{
	public function validate($value): bool
	{
		return true;
	}
}
