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
        Schema::create('atividades_professores', function (Blueprint $table) {
        $table->id();
        $table->foreignId('professor_id')->constrained('users')->onDelete('cascade'); // Relacionamento com usuário (professor)
        $table->foreignId('atividade_id')->constrained('atividades')->onDelete('cascade'); // Relacionamento com atividade
        $table->timestamps(); // Created_at e updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('atividades_professores');
    }
};
