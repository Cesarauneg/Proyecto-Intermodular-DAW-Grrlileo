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
        Schema::create('fish', function (Blueprint $table) {
            $table->id();

            // Datos básicos
            $table->string('file_name')->unique();  // corresponde a "file-name"
            $table->string('location')->nullable(); // "River", "Pond", etc.
            $table->string('shadow')->nullable();   // tamaño de la sombra, ej: "Smallest (1)"
            $table->string('rarity')->nullable();   // ej: "Common"

            // Disponibilidad
            $table->json('month_array_northern')->nullable(); // [11,12,1,2,3]
            $table->json('month_array_southern')->nullable(); // [5,6,7,8,9]
            $table->json('time_array')->nullable();           // [0,1,2,...23]
            $table->boolean('is_all_day')->default(false);    // true si aparece todo el día
            $table->boolean('is_all_year')->default(false);   // true si aparece todo el año

        
            // Precio
            $table->integer('price')->nullable();    // precio normal
            $table->integer('price_cj')->nullable(); // precio con C.J.

     
            // Nombres y frases
            $table->string('name_en');
            $table->string('name_es')->nullable();
            $table->string('catch_phrase_en')->nullable();
            $table->text('museum_phrase_en')->nullable();

           
            // Imágenes locales
            $table->string('image')->nullable(); 
            $table->string('icon')->nullable(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fish');
    }
};
