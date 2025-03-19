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
        Schema::create('perguntas_simulados', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('simulado_id'); // Chave estrangeira para provas
            $table->unsignedBigInteger('pergunta_id'); // Chave estrangeira para questões
            $table->timestamps();

            // Chaves estrangeiras
            $table->foreign('simulado_id')->references('id')->on('simulados')->onDelete('cascade');
            $table->foreign('pergunta_id')->references('id')->on('perguntas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perguntas_simulados');
    }
};
