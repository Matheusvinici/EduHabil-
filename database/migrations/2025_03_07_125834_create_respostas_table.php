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
            $table->unsignedBigInteger('user_id'); // Aluno que respondeu
            $table->unsignedBigInteger('questao_id');
            $table->unsignedBigInteger('prova_id');

            $table->string('resposta'); // A, B, C ou D
            $table->boolean('correta')->default(false); // Se a resposta está correta
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
