<?php
namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait HasAvailability
{   
    /**
     * Scope a query to only include items available now.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $hemisphere
     * @return \Illuminate\Database\Eloquent\Builder
     */  
    public function scopeAvailableNow(
        Builder $query,
        string $hemisphere = 'north'
    ) {
        $month = now()->month;
        $hour = now()->hour;

        $monthField = $hemisphere === 'south'
            ? 'month_array_southern'
            : 'month_array_northern';

        return $query
            ->where(function ($q) use ($month, $monthField) {
                $q->whereJsonContains($monthField, $month)
                  ->orWhere('is_all_year', true);
            })
            ->where(function ($q) use ($hour) {
                $q->whereJsonContains('time_array', $hour)
                  ->orWhere('is_all_day', true);
            });
    }
}
