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
        Schema::create('user_escola', function (Blueprint $table) {
            $table->id();

            // As colunas abaixo DEVEM ser unsignedBigInteger
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('escola_id');

            // Constraints corretas
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('escola_id')->references('id')->on('escolas')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_escola');
    }
};
