<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sea_Creature extends Model
{
        // Forzar el nombre de tabla correcto
    protected $table = 'sea_creatures';

    // Permitir asignación masiva
    protected $fillable = ['file_name', 'name_en', 'name_es', 'buy_price', 'sell_price', 'is_orderable', 'image_uri'];
}
