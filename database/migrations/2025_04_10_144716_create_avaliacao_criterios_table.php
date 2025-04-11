<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        //Tabela Pivô
        Schema::create('avaliacao_criterios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('avaliacao_tutoria_id')->constrained('tutoria_avaliacoes')->onDelete('cascade');
            $table->foreignId('criterio_avaliacao_id')->constrained('tutoria_criterios')->onDelete('cascade');
            $table->integer('nota'); // ou float, se usar notas com decimal
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('avaliacao_criterios');
    }
};
