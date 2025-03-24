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
        Schema::create('respostas_simulados', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // Aluno que respondeu
            $table->unsignedBigInteger('professor_id'); // Professor da turma do aluno (referencia users)
            $table->unsignedBigInteger('escola_id'); // Escola do aluno
            $table->unsignedBigInteger('simulado_id'); // Simulado respondido
            $table->unsignedBigInteger('pergunta_id'); // Pergunta respondida
            $table->enum('resposta', ['A', 'B', 'C', 'D']); // Resposta do aluno
            $table->boolean('correta'); // Indica se a resposta está correta
            $table->timestamps();
        
            // Chaves estrangeiras
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('professor_id')->references('id')->on('users')->onDelete('cascade'); // Referencia users
            $table->foreign('escola_id')->references('id')->on('escolas')->onDelete('cascade');
            $table->foreign('simulado_id')->references('id')->on('simulados')->onDelete('cascade');
            $table->foreign('pergunta_id')->references('id')->on('perguntas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('respostas_simulados');
    }
};