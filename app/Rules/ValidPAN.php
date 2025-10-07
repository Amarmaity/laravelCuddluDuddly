<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidPAN implements Rule
{
    public function passes($attribute, $value)
    {
        return preg_match("/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/", $value);
    }

    public function message()
    {
        return 'The :attribute is not a valid PAN format.';
    }
}
