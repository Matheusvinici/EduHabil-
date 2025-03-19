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
        
    Schema::create('perguntas', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('ano_id'); // Chave estrangeira para anos
        $table->unsignedBigInteger('disciplina_id'); // Chave estrangeira para disciplinas
        $table->unsignedBigInteger('habilidade_id'); // Chave estrangeira para habilidades

        $table->text('enunciado'); // Enunciado da questão
        $table->string('alternativa_a'); // Alternativa A
        $table->string('alternativa_b'); // Alternativa B
        $table->string('alternativa_c'); // Alternativa C
        $table->string('alternativa_d'); // Alternativa D
        $table->enum('resposta_correta', ['A', 'B', 'C', 'D']); // Resposta correta
        $table->timestamps();

        // Chaves estrangeiras
        $table->foreign('ano_id')->references('id')->on('anos')->onDelete('cascade');
        $table->foreign('disciplina_id')->references('id')->on('disciplinas')->onDelete('cascade');
        $table->foreign('habilidade_id')->references('id')->on('habilidades')->onDelete('cascade');

    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perguntas');
    }
};
