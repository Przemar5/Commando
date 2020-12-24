<?php

namespace Commando\Validators;

interface Validator
{
	public function validate($value): bool;
}