<?php

namespace App\Models;

class Product extends BaseModel
{
    protected $fillable = [
        'name',
        'description',
        'price',
        'del_flag',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'del_flag' => \App\Enums\DeleteFlag::class,
    ];
}
