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
        Schema::create('adaptacao_deficiencia', function (Blueprint $table) {
            $table->id();
            $table->foreignId('adaptacao_id')->constrained('adaptacoes')->onDelete('cascade');
            $table->foreignId('deficiencia_id')->constrained('deficiencias')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('adaptacao_deficiencia');
    }
};
