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
        Schema::create('fossils', function (Blueprint $table) {
            $table->id();

            // Datos básicos
            $table->string('file_name')->unique();  // "file-name"
            $table->string('part_of')->nullable();  // "part-of", si es parte de un fósil con varias piezas

            // Precio
            $table->integer('price')->nullable();

            $table->string('name_en');
            $table->string('name_es')->nullable();
            $table->text('museum_phrase_en')->nullable();

            // Imágenes locales
            $table->string('image')->nullable(); // ejemplo: images/fossils/acanthostega.png

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fossils');
    }
};
