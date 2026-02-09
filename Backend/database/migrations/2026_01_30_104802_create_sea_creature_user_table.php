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
        Schema::create('sea_creature_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sea_creature_id')->constrained()->cascadeOnDelete();

            // Campos extra (MUY importante)
            $table->boolean('donated_to_museum')->default(false);
            $table->timestamps();

            $table->unique(['user_id', 'sea_creature_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sea_creature_user');
    }
};
