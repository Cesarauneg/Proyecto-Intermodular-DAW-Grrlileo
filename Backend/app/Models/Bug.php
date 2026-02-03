<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasAvailability;

class Bug extends Model
{
    use HasAvailability;

    protected $casts = [
        'month_array_northern' => 'array',
        'month_array_southern' => 'array',
        'time_array' => 'array',
        'is_all_day' => 'boolean',
        'is_all_year' => 'boolean',
    ];
}
