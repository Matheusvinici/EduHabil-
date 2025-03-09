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
        Schema::create('unidades', function (Blueprint $table) {
            $table->id();
            $table->string('nome'); // Unidade I, Unidade II, etc.
            $table->timestamps();

            // Chave estrangeira
        });

                // Inserindo os valores padrão
            DB::table('unidades')->insert([
                ['nome' => 'Unidade I'],
                ['nome' => 'Unidade II'],
                ['nome' => 'Unidade III'],
                ['nome' => 'Unidade IV'],
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unidades');
    }
};
