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
        Schema::create('provas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // Professor que criou a prova
            $table->unsignedBigInteger('ano_id');
            $table->unsignedBigInteger('disciplina_id');
            $table->unsignedBigInteger('habilidade_id');
            $table->unsignedBigInteger('unidade_id');
            $table->string('nome'); // Nome da prova
            $table->date('data')->nullable();
            $table->text('observacoes')->nullable();
            $table->timestamps();

            // Chaves estrangeiras
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('ano_id')->references('id')->on('anos')->onDelete('cascade');
            $table->foreign('habilidade_id')->references('id')->on('habilidades')->onDelete('cascade');

            $table->foreign('disciplina_id')->references('id')->on('disciplinas')->onDelete('cascade');
            $table->foreign('unidade_id')->references('id')->on('unidades')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('provas');
    }
};
