<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hourly_Music extends Model
{
    // Forzar el nombre de tabla correcto
    protected $table = 'hourly_music';

    // Permitir asignación masiva
    protected $fillable = ['file_name', 'hour', 'weather', 'music_uri'];
}
