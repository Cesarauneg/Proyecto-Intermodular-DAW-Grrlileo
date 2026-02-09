<?php
namespace App\Models;

use App\Traits\HasAvailability;
use Illuminate\Database\Eloquent\Model;

class Fish extends Model
{
    use HasAvailability;

    protected $casts = [
        'month_array_northern' => 'array',
        'month_array_southern' => 'array',
        'time_array' => 'array',
        'is_all_day' => 'boolean',
        'is_all_year' => 'boolean',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
