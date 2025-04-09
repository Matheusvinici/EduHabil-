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
        Schema::create('criterios_avaliacao', function (Blueprint $table) {
            $table->id();
            $table->string('categoria'); // Ex: "Pedagógico", "Estrutura"
            $table->string('descricao');  // Ex: "Qualidade da merenda", "Estado das salas"
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('criterios_avaliacao');
    }
};
