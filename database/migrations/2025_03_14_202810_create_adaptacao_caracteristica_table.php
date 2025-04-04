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
        Schema::create('adaptacao_caracteristica', function (Blueprint $table) {
            $table->id();
            $table->foreignId('adaptacao_id')->constrained('adaptacoes');
            $table->foreignId('caracteristica_id')->constrained('caracteristicas');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('adaptacao_caracteristica');
    }
};
