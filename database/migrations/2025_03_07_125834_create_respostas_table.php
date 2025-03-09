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
        Schema::create('respostas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // Chave estrangeira para users (aluno)
            $table->unsignedBigInteger('prova_id'); // Chave estrangeira para provas
            $table->unsignedBigInteger('questao_id'); // Chave estrangeira para questões
            $table->enum('resposta', ['A', 'B', 'C', 'D']); // Resposta do aluno
            $table->boolean('correta'); // Indica se a resposta está correta
            $table->timestamps();

            // Chaves estrangeiras
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('prova_id')->references('id')->on('provas')->onDelete('cascade');
            $table->foreign('questao_id')->references('id')->on('questoes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('respostas');
    }
};