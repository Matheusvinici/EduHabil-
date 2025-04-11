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
            $table->foreignId('ano_id')->constrained('anos')->onDelete('cascade');
            $table->foreignId('disciplina_id')->constrained('disciplinas')->onDelete('cascade');
            $table->foreignId('habilidade_id')->constrained('habilidades')->onDelete('cascade');
            $table->text('enunciado');
            $table->string('alternativa_a');
            $table->string('alternativa_b');
            $table->string('alternativa_c');
            $table->string('alternativa_d');
            $table->enum('resposta_correta', ['A', 'B', 'C', 'D']);
            $table->string('imagem')->nullable(); // Coluna para a imagem (opcional)
            $table->integer('peso')->default(1);
             // Parâmetros TRI
             $table->decimal('tri_a', 5, 2)->default(1.0);
             $table->decimal('tri_b', 5, 2)->default(0.0);
             $table->decimal('tri_c', 5, 2)->default(0.2);
            $table->timestamps();
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
