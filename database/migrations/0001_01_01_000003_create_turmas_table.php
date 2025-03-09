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
    // Criar a tabela `turmas`
    Schema::create('turmas', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('escola_id'); // Chave estrangeira para escolas
        $table->unsignedBigInteger('professor_id'); // Chave estrangeira para users (professor)
        $table->string('nome_turma'); // Ex: "5º A"
        $table->integer('quantidade_alunos');
        $table->string('codigo_turma')->unique(); // Código único da turma
        $table->timestamps();

        // Chaves estrangeiras
        $table->foreign('escola_id')->references('id')->on('escolas')->onDelete('cascade');
        $table->foreign('professor_id')->references('id')->on('users')->onDelete('cascade');
    });

    // Adicionar a coluna `turma_id` à tabela `users`
    Schema::table('users', function (Blueprint $table) {
        $table->unsignedBigInteger('turma_id')->nullable(); // Chave estrangeira para turmas
        $table->foreign('turma_id')->references('id')->on('turmas')->onDelete('set null');
    });
}

public function down(): void
{
    // Remover a coluna `turma_id` da tabela `users`
    Schema::table('users', function (Blueprint $table) {
        $table->dropForeign(['turma_id']); // Remove a chave estrangeira
        $table->dropColumn('turma_id'); // Remove a coluna
    });

    // Remover a tabela `turmas`
    Schema::dropIfExists('turmas');
}

    /**
     * Reverse the migrations.
     */
   
};