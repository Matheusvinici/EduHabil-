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
        Schema::create('professor_turma', function (Blueprint $table) {
            $table->unsignedBigInteger('professor_id');
            $table->unsignedBigInteger('turma_id');
            
            $table->foreign('professor_id')->references('id')->on('users');
            $table->foreign('turma_id')->references('id')->on('turmas');
            
            $table->primary(['professor_id', 'turma_id']); // Chave composta
                    $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('professor_turma');
    }
};
