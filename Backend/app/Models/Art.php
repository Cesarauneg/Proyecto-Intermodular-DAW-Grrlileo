<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Art extends Model
{
    protected $table = 'art';

    protected $casts = [
        'has_fake' => 'boolean',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'art_user')
            ->withPivot('donated_to_museum')
            ->withTimestamps();
    }
}
