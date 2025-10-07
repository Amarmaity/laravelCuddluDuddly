<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidGSTIN implements Rule
{
    public function passes($attribute, $value)
    {
        return preg_match("/^([0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1})$/", $value);
    }

    public function message()
    {
        return 'The :attribute is not a valid GSTIN format.';
    }
}
