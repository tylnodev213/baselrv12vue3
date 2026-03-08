<?php

namespace App\Validators;

/**
 * Custom validation rules for the application
 * 
 * This class provides helper methods to use custom validators
 * Can be registered in a service provider or used directly
 */
class ValidatorRules
{
    /**
     * Create a CustomExists validator instance
     *
     * @param string $table Table name
     * @param string $column Column name (default: 'id')
     * @return CustomExists
     */
    public static function exists($table, $column = 'id')
    {
        return new CustomExists($table, $column);
    }

    /**
     * Create a CustomUnique validator instance
     *
     * @param string $table Table name
     * @param string $column Column name (default: 'id')
     * @return CustomUnique
     */
    public static function unique($table, $column = 'id')
    {
        return new CustomUnique($table, $column);
    }

    /**
     * Create a CustomPassword validator instance
     *
     * @param int $minLength Minimum password length (default: 8)
     * @return CustomPassword
     */
    public static function password($minLength = 8)
    {
        return new CustomPassword($minLength);
    }
}
