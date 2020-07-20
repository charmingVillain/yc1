<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class OnlyAlphaDash implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return empty($value) || preg_match("/^[a-zA-Z0-9\-\_]+$/", $value) > 0;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return '验证字段只能包含字母、数字，以及破折号 (-) 和下划线 ( _ )';
    }
}
