<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fossil extends Model
{
    protected $table = 'fossils';

    public function users()
    {
        return $this->belongsToMany(User::class, 'fossil_user')
            ->withPivot('donated_to_museum')
            ->withTimestamps();
    }
}
