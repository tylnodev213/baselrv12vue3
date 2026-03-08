<?php

namespace App\Validators;

/**
 * CustomPassword Validator
 * 
 * Validates password requirements:
 * - Minimum 8 characters
 * - At least one uppercase letter
 * - At least one lowercase letter
 * - At least one digit
 * - At least one special character
 * 
 * Usage:
 *   'password' => new CustomPassword()
 *   'password' => new CustomPassword(minLength: 12) // Custom minimum length
 */
class CustomPassword extends BaseValidator
{
    /**
     * @var int Minimum password length
     */
    protected $minLength = 8;

    /**
     * @var bool Require uppercase letter
     */
    protected $requireUppercase = true;

    /**
     * @var bool Require lowercase letter
     */
    protected $requireLowercase = true;

    /**
     * @var bool Require digit
     */
    protected $requireDigit = true;

    /**
     * @var bool Require special character
     */
    protected $requireSpecial = true;

    /**
     * Constructor
     *
     * @param int $minLength Minimum password length (default: 8)
     */
    public function __construct($minLength = 8)
    {
        $this->minLength = $minLength;
        $this->message = "Password must be at least {$minLength} characters and contain uppercase, lowercase, digit, and special character.";
    }

    /**
     * Set minimum password length
     *
     * @param int $length
     * @return $this
     */
    public function minLength($length)
    {
        $this->minLength = $length;

        return $this;
    }

    /**
     * Require uppercase letter
     *
     * @param bool $require
     * @return $this
     */
    public function requireUppercase($require = true)
    {
        $this->requireUppercase = $require;

        return $this;
    }

    /**
     * Require lowercase letter
     *
     * @param bool $require
     * @return $this
     */
    public function requireLowercase($require = true)
    {
        $this->requireLowercase = $require;

        return $this;
    }

    /**
     * Require digit
     *
     * @param bool $require
     * @return $this
     */
    public function requireDigit($require = true)
    {
        $this->requireDigit = $require;

        return $this;
    }

    /**
     * Require special character
     *
     * @param bool $require
     * @return $this
     */
    public function requireSpecial($require = true)
    {
        $this->requireSpecial = $require;

        return $this;
    }

    /**
     * Determine if the validation rule passes
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // Check minimum length
        if (strlen($value) < $this->minLength) {
            return false;
        }

        // Check uppercase letter
        if ($this->requireUppercase && !preg_match('/[A-Z]/', $value)) {
            return false;
        }

        // Check lowercase letter
        if ($this->requireLowercase && !preg_match('/[a-z]/', $value)) {
            return false;
        }

        // Check digit
        if ($this->requireDigit && !preg_match('/[0-9]/', $value)) {
            return false;
        }

        // Check special character
        if ($this->requireSpecial && !preg_match('/[!@#$%^&*(),.?":{}|<>]/', $value)) {
            return false;
        }

        return true;
    }
}
