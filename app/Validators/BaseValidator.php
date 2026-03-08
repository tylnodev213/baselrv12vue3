<?php

namespace App\Validators;

use Illuminate\Contracts\Validation\Rule;

/**
 * Base Validator class
 * Provides common validation logic for custom validators
 */
abstract class BaseValidator implements Rule
{
    /**
     * Validation error message
     *
     * @var string
     */
    protected $message = 'Validation failed';

    /**
     * Get validation error message
     *
     * @return string
     */
    public function message()
    {
        return $this->message;
    }

    /**
     * Determine if the validation rule passes
     * Must be implemented by child classes
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    abstract public function passes($attribute, $value);
}
