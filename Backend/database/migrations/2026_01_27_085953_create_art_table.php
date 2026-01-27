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
        Schema::create('art', function (Blueprint $table) {
            $table->id();

            // Datos básicos
            $table->string('file_name')->unique(); // corresponde a "file-name"
            $table->boolean('has_fake')->default(false);
            $table->integer('buy_price')->nullable();
            $table->integer('sell_price')->nullable();

            // Nombres en inglés y español
            $table->string('name_en');
            $table->string('name_es');

            // Descripción en museo 
            $table->text('museum_desc_en')->nullable();

            // Imágenes locales (public/images/art)
            $table->string('image')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('art');
    }
};
