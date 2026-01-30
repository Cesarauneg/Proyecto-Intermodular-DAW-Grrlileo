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
        Schema::table('users', function (Blueprint $table) {
            //Añadir los nuevos campos al modelo de usuario
            $table->string('avatar')->nullable();
            $table->text('bio')->nullable();
            $table->string('hemisphere')->nullable();
            $table->string('island_name')->nullable();
            $table->string('island_fruit')->nullable();
            $table->unsignedBigInteger('money')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {

        });
    }
};
