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
        Schema::create('questoes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ano_id');
            $table->unsignedBigInteger('disciplina_id');
            $table->unsignedBigInteger('habilidade_id');
            $table->unsignedBigInteger('unidade_id'); // Adicionado
            $table->text('enunciado');
            $table->string('alternativa_a');
            $table->string('alternativa_b');
            $table->string('alternativa_c');
            $table->string('alternativa_d');
            $table->string('resposta_correta'); // A, B, C ou D
            $table->timestamps();

            // Chaves estrangeiras
            $table->foreign('ano_id')->references('id')->on('anos')->onDelete('cascade');
            $table->foreign('disciplina_id')->references('id')->on('disciplinas')->onDelete('cascade');
            $table->foreign('habilidade_id')->references('id')->on('habilidades')->onDelete('cascade');
            $table->foreign('unidade_id')->references('id')->on('unidades')->onDelete('cascade'); // Adicionado
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questoes');
    }
};