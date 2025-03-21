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
        Schema::create('simulados', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ano_id'); // Chave estrangeira para anos
            $table->unsignedBigInteger('user_id'); // Chave estrangeira para users (professor)
            $table->unsignedBigInteger('disciplina_id'); // Chave estrangeira para disciplinas
            $table->unsignedBigInteger('habilidade_id'); // Chave estrangeira para habilidades
            $table->string('nome'); // Nome da prova
            $table->date('data'); // Data da prova
            $table->text('observacoes')->nullable(); // Observações da prova
            $table->timestamps();

            // Chaves estrangeiras
            $table->foreign('ano_id')->references('id')->on('anos')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('disciplina_id')->references('id')->on('disciplinas')->onDelete('cascade');
            $table->foreign('habilidade_id')->references('id')->on('habilidades')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('simulados');
    }
};
