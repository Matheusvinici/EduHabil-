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
        Schema::create('simulados', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ano_id'); // Chave estrangeira para anos
            $table->string('nome');
            $table->text('descricao')->nullable();
            $table->integer('tempo_limite')->nullable()->comment('Tempo em minutos');
            $table->timestamps();

            // Chave estrangeira para anos
            $table->foreign('ano_id')->references('id')->on('anos')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('simulados');
    }
};
