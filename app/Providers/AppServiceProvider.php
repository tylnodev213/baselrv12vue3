<?php

namespace App\Providers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->registerCustomValidationRules();
    }

    /**
     * Register custom validation rules
     *
     * @return void
     */
    private function registerCustomValidationRules()
    {
        /**
         * Custom Exists Validator
         * Usage: 'field' => 'custom_exists:users,id' or 'custom_exists:users,id,ignore_field,ignore_value'
         * Validates that a value exists in a table and is NOT soft deleted (del_flag = 0)
         */
        Validator::extend('custom_exists', function ($attribute, $value, $parameters, $validator) {
            if (count($parameters) < 2) {
                return false;
            }

            $table = $parameters[0];
            $column = $parameters[1];

            $query = \Illuminate\Support\Facades\DB::table($table)
                ->where($column, $value)
                ->where('del_flag', 0); // Not deleted

            // If ignore parameters provided: ignore_field and ignore_value
            if (count($parameters) >= 4) {
                $ignoreField = $parameters[2];
                $ignoreValue = $parameters[3];
                $query->where($ignoreField, '!=', $ignoreValue);
            }

            return $query->exists();
        });

        Validator::replacer('custom_exists', function ($message, $attribute, $rule, $parameters) {
            return str_replace(':attribute', $attribute, 'The selected :attribute value does not exist or has been deleted.');
        });

        /**
         * Custom Unique Validator
         * Usage: 'field' => 'custom_unique:users,email' or 'custom_unique:users,email,id,user_id'
         * Validates that a value is unique in a table and only checks non-deleted records (del_flag = 0)
         */
        Validator::extend('custom_unique', function ($attribute, $value, $parameters, $validator) {
            if (count($parameters) < 2) {
                return false;
            }

            $table = $parameters[0];
            $column = $parameters[1];

            $query = \Illuminate\Support\Facades\DB::table($table)
                ->where($column, $value)
                ->where('del_flag', 0); // Not deleted

            // If ignore parameters provided: ignore_field and ignore_value
            if (count($parameters) >= 4) {
                $ignoreField = $parameters[2];
                $ignoreValue = $parameters[3];
                $query->where($ignoreField, '!=', $ignoreValue);
            }

            return !$query->exists();
        });

        Validator::replacer('custom_unique', function ($message, $attribute, $rule, $parameters) {
            return str_replace(':attribute', $attribute, 'The :attribute has already been taken.');
        });

        /**
         * Custom Password Validator
         * Usage: 'password' => 'custom_password' or 'custom_password:12' (for min length)
         * 
         * Default requirements:
         * - Minimum 8 characters (or specified length)
         * - At least one uppercase letter
         * - At least one lowercase letter
         * - At least one digit
         * - At least one special character
         */
        Validator::extend('custom_password', function ($attribute, $value, $parameters, $validator) {
            $minLength = !empty($parameters) ? (int)$parameters[0] : 8;

            // Check minimum length
            if (strlen($value) < $minLength) {
                return false;
            }

            // Check uppercase letter
            if (!preg_match('/[A-Z]/', $value)) {
                return false;
            }

            // Check lowercase letter
            if (!preg_match('/[a-z]/', $value)) {
                return false;
            }

            // Check digit
            if (!preg_match('/[0-9]/', $value)) {
                return false;
            }

            // Check special character
            if (!preg_match('/[!@#$%^&*(),.?":{}|<>]/', $value)) {
                return false;
            }

            return true;
        });

        Validator::replacer('custom_password', function ($message, $attribute, $rule, $parameters) {
            $minLength = !empty($parameters) ? (int)$parameters[0] : 8;
            return "The {$attribute} must be at least {$minLength} characters and contain uppercase, lowercase, digit, and special character.";
        });
    }
}
