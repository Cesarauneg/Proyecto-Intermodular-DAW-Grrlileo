<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bugs', function (Blueprint $table) {
            $table->id();

            // Datos básicos
            $table->string('file_name')->unique();
            $table->string('location')->nullable();
            $table->string('rarity')->nullable();

            // Disponibilidad como arrays JSON
            $table->json('month_array_northern')->nullable(); // [9,10,11,...]
            $table->json('month_array_southern')->nullable(); // [3,4,5,...]
            $table->json('time_array')->nullable();             // [4,5,6,...]
            $table->boolean('is_all_day')->default(false);
            $table->boolean('is_all_year')->default(false);

            // Precio
            $table->integer('price')->nullable();
            $table->integer('price_flick')->nullable();

            // Nombres y textos
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

    public function down(): void
    {
        Schema::dropIfExists('bugs');
    }
};
