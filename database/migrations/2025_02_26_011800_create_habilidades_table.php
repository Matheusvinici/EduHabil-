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
            $table->unsignedBigInteger('ano_id'); // Chave estrangeira para anos
            $table->unsignedBigInteger('disciplina_id'); // Chave estrangeira para disciplinas
           
            $table->string('descricao'); // Descrição da habilidade
            $table->timestamps();

            // Chaves estrangeiras
            $table->foreign('ano_id')->references('id')->on('anos')->onDelete('cascade');
            $table->foreign('disciplina_id')->references('id')->on('disciplinas')->onDelete('cascade');
            
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