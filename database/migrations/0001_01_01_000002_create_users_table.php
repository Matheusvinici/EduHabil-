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
    Schema::create('users', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('email')->unique(); // E-mail único para todos (opcional)
        $table->string('password');
        $table->enum('role', ['admin', 'professor', 'aluno', 'aee', 'inclusiva', 'coordenador'])->default('professor');
        $table->unsignedBigInteger('escola_id')->nullable(); // Chave estrangeira para escolas
        
        $table->string('codigo_acesso')->nullable(); // Código de acesso do aluno
        $table->timestamps();

        // Chave estrangeira para escolas
        $table->foreign('escola_id')->references('id')->on('escolas')->onDelete('set null');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};