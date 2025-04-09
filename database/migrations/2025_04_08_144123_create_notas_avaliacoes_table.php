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
        Schema::create('notas_avaliacoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('avaliacao_id')->constrained('avaliacoes_tutoria');
            $table->foreignId('criterio_id')->constrained('criterios_avaliacao');
            $table->integer('nota'); // 1 a 5 (1=Ruim, 5=Excelente)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notas_avaliacoes');
    }
};
