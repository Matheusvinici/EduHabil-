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
        Schema::create('atividades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('disciplina_id')->constrained('disciplinas')->onDelete('cascade'); // Relacionamento com disciplina
            $table->foreignId('ano_id')->constrained('anos')->onDelete('cascade'); // Relacionamento com ano
            $table->foreignId('habilidade_id')->constrained('habilidades')->onDelete('cascade'); // Relacionamento com habilidade
            $table->string('titulo'); // Título da atividade
            $table->text('objetivo'); // Objetivo da atividade
            $table->text('metodologia'); // Metodologia de ensino
            $table->text('materiais'); // Materiais necessários
            $table->text('resultados_esperados'); // Resultados esperados
            $table->timestamps(); // Created_at e updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('atividades');
    }
};
