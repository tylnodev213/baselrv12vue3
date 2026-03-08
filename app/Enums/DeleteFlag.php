<?php

namespace App\Enums;

enum DeleteFlag: int
{
    case OFF = 0;
    case ON = 1;

    public function label(): string
    {
        return match ($this) {
            self::OFF => 'Active',
            self::ON => 'Deleted',
        };
    }

    public function isDeleted(): bool
    {
        return $this === self::ON;
    }
}
