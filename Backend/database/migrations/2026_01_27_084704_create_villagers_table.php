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
        Schema::create('villagers', function (Blueprint $table) {
            $table->id();

            // Datos básicos
            $table->string('file_name')->unique(); // Nombre del archivo único
            $table->string('personality');
            $table->string('birthday_string');
            $table->string('birthday');
            $table->string('species');
            $table->string('gender');
            $table->string('subtype')->nullable();
            $table->string('hobby')->nullable();
            $table->string('bubble_color')->nullable();
            $table->string('text_color')->nullable();
            $table->string('saying')->nullable();

            // Nombres en inglés y español
            $table->string('name_en');
            $table->string('name_es');

            // Catch phrases en inglés y español
            $table->string('catch_en')->nullable();
            $table->string('catch_es')->nullable();

            // Imágenes locales (public/images/villagers)
            // Iconos locales (public/icons/villagers)
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
        Schema::dropIfExists('villagers');
    }
};
