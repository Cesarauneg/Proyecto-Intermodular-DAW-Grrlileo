<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Totakeke_Music extends Model
{
        // Forzar el nombre de tabla correcto
    protected $table = 'totakeke_music';

    // Permitir asignación masiva
    protected $fillable = ['file_name', 'name_en', 'name_es', 'buy_price', 'sell_price', 'is_orderable', 'music_uri', 'image'];
}
