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
        Schema::create('tutoria_acompanhamentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('avaliacao_id')->constrained('tutoria_avaliacoes');
            $table->foreignId('criterio_id')->constrained('tutoria_criterios');
            $table->enum('prioridade', ['alta', 'media', 'baixa']);
            $table->text('acao_melhoria');
            $table->foreignId('responsavel_id')->constrained('users');
            $table->date('prazo');
            $table->enum('status', ['pendente', 'em_andamento', 'concluido'])->default('pendente');
            $table->text('observacoes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tutoria_acompanhamentos');
    }
};
