<?php

namespace App\Validators;

use Illuminate\Support\Facades\DB;

/**
 * CustomUnique Validator
 * 
 * Validates that a value is unique in a database table
 * and only checks non-deleted records (del_flag = 0)
 * 
 * Usage:
 *   'email' => new CustomUnique('users', 'email')
 *   'slug' => new CustomUnique('products', 'slug')
 * 
 * For updates (ignore current record):
 *   'email' => (new CustomUnique('users', 'email'))->ignoring('id', $userId)
 */
class CustomUnique extends BaseValidator
{
    /**
     * @var string Database table name
     */
    protected $table;

    /**
     * @var string Column name to check uniqueness
     */
    protected $column;

    /**
     * @var string Column to ignore in uniqueness check
     */
    protected $ignoreColumn;

    /**
     * @var mixed Value to ignore
     */
    protected $ignoreValue;

    /**
     * Constructor
     *
     * @param string $table Table name
     * @param string $column Column name (default: 'id')
     */
    public function __construct($table, $column = 'id')
    {
        $this->table = $table;
        $this->column = $column;
        $this->message = "The {$this->column} has already been taken.";
    }

    /**
     * Ignore a specific record by column and value
     * Useful for updates where you want to exclude the current record
     *
     * @param string $column
     * @param mixed $value
     * @return $this
     */
    public function ignoring($column, $value)
    {
        $this->ignoreColumn = $column;
        $this->ignoreValue = $value;

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
        $query = DB::table($this->table)
            ->where($this->column, $value)
            ->where('del_flag', 0); // Not deleted

        if ($this->ignoreColumn && $this->ignoreValue !== null) {
            $query->where($this->ignoreColumn, '!=', $this->ignoreValue);
        }

        return !$query->exists();
    }
}
