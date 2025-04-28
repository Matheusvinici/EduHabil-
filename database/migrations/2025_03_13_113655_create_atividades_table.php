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
            $table->foreignId('ano_id')->constrained('anos')->onDelete('cascade');
            $table->string('titulo');
            $table->text('objetivo');
            $table->text('metodologia');
            $table->text('materiais');
            $table->text('resultados_esperados');
            $table->text('links_sugestoes')->nullable();
            $table->timestamps();
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
