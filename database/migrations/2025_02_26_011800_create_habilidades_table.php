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
        Schema::create('habilidades', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ano_id');
            $table->unsignedBigInteger('disciplina_id');
            $table->unsignedBigInteger('unidade_id'); // Nova chave para unidade
            $table->string('descricao'); // Ex: "Resolver problemas matemáticos simples"
            $table->timestamps();

            // Chaves estrangeiras
            $table->foreign('ano_id')->references('id')->on('anos')->onDelete('cascade');
            $table->foreign('disciplina_id')->references('id')->on('disciplinas')->onDelete('cascade');
            $table->foreign('unidade_id')->references('id')->on('unidades')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('habilidades');
    }
};
