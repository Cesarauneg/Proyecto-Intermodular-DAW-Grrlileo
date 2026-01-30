<?php
namespace App\Models;

use App\Traits\HasAvailability;
use Illuminate\Database\Eloquent\Model;

class Fish extends Model
{
    //
    use HasAvailability;
    public function users()
    {
        return $this->belongsToMany(User::class);
    }

}
