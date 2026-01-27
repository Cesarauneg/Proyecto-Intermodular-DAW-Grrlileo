<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('hourly_music', function (Blueprint $table) {
            $table->id();

            // Datos de la música
            $table->string('file_name')->unique(); // "BGM_24Hour_00_Rainy"
            $table->tinyInteger('hour');           // hora del día (0-23)
            $table->string('weather')->nullable();  // tipo de clima: Rainy, Snowy, Sunny...
            $table->string('music_uri')->nullable(); // URL o ruta local de la canción

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hourly_music');
    }
};
