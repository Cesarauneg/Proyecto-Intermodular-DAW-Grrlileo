<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Villager extends Model
{
    // Ensure the table is 'villagers' if not following default naming convention
    // protected $table = 'villagers';

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['image_url']; // Append the new accessor name

    /**
     * Get the villager's image URL.
     */
    protected function imageUrl(): Attribute // Renamed method
    {
        return Attribute::make(
            get: fn (string $value, array $attributes) => asset('images/villagers/' . $attributes['image']), // Access original 'image' attribute
        );
    }
}