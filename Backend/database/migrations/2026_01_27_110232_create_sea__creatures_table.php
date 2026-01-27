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
        Schema::create('sea_creatures', function (Blueprint $table) {
            $table->id();

            // -------------------
            // Datos básicos
            // -------------------
            $table->string('file_name')->unique(); // "file-name"
            $table->string('speed')->nullable();   // "Stationary", "Fast", etc.
            $table->string('shadow')->nullable();  // tamaño de sombra

            // -------------------
            // Disponibilidad
            // -------------------
            $table->json('month_array_northern')->nullable(); // meses norte
            $table->json('month_array_southern')->nullable(); // meses sur
            $table->json('time_array')->nullable();           // horas disponibles
            $table->boolean('is_all_day')->default(false);
            $table->boolean('is_all_year')->default(false);

            // -------------------
            // Precio
            // -------------------
            $table->integer('price')->nullable();

            // -------------------
            // Nombres y textos
            // -------------------
            $table->string('name_en');
            $table->string('name_es')->nullable();
            $table->string('catch_phrase_en')->nullable();
            $table->string('catch_phrase_es')->nullable();
            $table->text('museum_phrase_en')->nullable();
            $table->text('museum_phrase_es')->nullable();

            // -------------------
            // Imágenes locales
            // -------------------
            $table->string('image')->nullable(); // ejemplo: images/sea/seaweed.png
            $table->string('icon')->nullable();  // ejemplo: images/sea/icons/seaweed.png

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sea_creatures');
    }
};
