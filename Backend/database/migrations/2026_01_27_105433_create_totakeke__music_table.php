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
        Schema::create('totakeke_music', function (Blueprint $table) {
            $table->id();

            // Datos básicos
            $table->string('file_name')->unique(); // "mjk_Keiji"
            
            // Nombres
            $table->string('name_en');
            $table->string('name_es')->nullable();


            // Precios y disponibilidad
            $table->integer('buy_price')->nullable();
            $table->integer('sell_price')->nullable();
            $table->boolean('is_orderable')->default(false);


            // Rutas de música e imágenes
            $table->string('music_uri')->nullable(); // URL o ruta local del audio
            $table->string('image')->nullable();     // URL o ruta local de la portada

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('totakeke_music');
    }
};
