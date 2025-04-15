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
        Schema::create('tutoria_avaliacoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tutor_id');
            $table->foreignId('escola_id');
            $table->date('data_visita');
            $table->text('observacoes')->nullable(); // Anotações livres
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tutoria_avaliacoes');
    }
};
