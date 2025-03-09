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
        Schema::create('questoes_provas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('prova_id'); // Chave estrangeira para provas
            $table->unsignedBigInteger('questao_id'); // Chave estrangeira para questões
            $table->timestamps();

            // Chaves estrangeiras
            $table->foreign('prova_id')->references('id')->on('provas')->onDelete('cascade');
            $table->foreign('questao_id')->references('id')->on('questoes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questoes_provas');
    }
};