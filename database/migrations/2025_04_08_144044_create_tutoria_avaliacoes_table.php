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
        Schema::create('avaliacoes_tutoria', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tutor_id')->constrained('users');
            $table->foreignId('escola_id')->constrained('escolas');
            $table->date('data_visita');
            $table->text('observacoes')->nullable(); // Anotações livres
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('avaliacoes_tutoria');
    }
};
