<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasAvailability;

class Sea_Creature extends Model
{
    use HasAvailability;

    protected $table = 'sea_creatures';

    protected $fillable = ['file_name', 'name_en', 'name_es', 'buy_price', 'sell_price', 'is_orderable', 'image_uri'];

    protected $casts = [
        'month_array_northern' => 'array',
        'month_array_southern' => 'array',
        'time_array' => 'array',
        'is_all_day' => 'boolean',
        'is_all_year' => 'boolean',
    ];
}
