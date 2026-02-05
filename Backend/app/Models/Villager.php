<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Villager extends Model
{
    protected $table = 'villagers';

    public function users()
    {
        return $this->belongsToMany(User::class)
            ->withPivot('is_favorite')
            ->withTimestamps();
    }
}
