<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class Team extends BaseModel
{
    protected $fillable = [
        'name',
        'description',
        'del_flag',
    ];

    /**
     * Get users in this team
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
