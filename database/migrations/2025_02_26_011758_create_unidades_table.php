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
            $table->unsignedBigInteger('ano_id'); // Relacionamento com o ano escolar
            $table->string('nome'); // Unidade I, Unidade II, etc.
            $table->timestamps();

            // Chave estrangeira
            $table->foreign('ano_id')->references('id')->on('anos')->onDelete('cascade');
        });

        // Inserindo os valores padrão
        DB::table('unidades')->insert([
            ['ano_id' => 1, 'nome' => 'Unidade I'],
            ['ano_id' => 1, 'nome' => 'Unidade II'],
            ['ano_id' => 1, 'nome' => 'Unidade III'],
            ['ano_id' => 1, 'nome' => 'Unidade IV'],

            ['ano_id' => 2, 'nome' => 'Unidade I'],
            ['ano_id' => 2, 'nome' => 'Unidade II'],
            ['ano_id' => 2, 'nome' => 'Unidade III'],
            ['ano_id' => 2, 'nome' => 'Unidade IV'],

            ['ano_id' => 3, 'nome' => 'Unidade I'],
            ['ano_id' => 3, 'nome' => 'Unidade II'],
            ['ano_id' => 3, 'nome' => 'Unidade III'],
            ['ano_id' => 3, 'nome' => 'Unidade IV'],

            ['ano_id' => 4, 'nome' => 'Unidade I'],
            ['ano_id' => 4, 'nome' => 'Unidade II'],
            ['ano_id' => 4, 'nome' => 'Unidade III'],
            ['ano_id' => 4, 'nome' => 'Unidade IV'],

            ['ano_id' => 5, 'nome' => 'Unidade I'],
            ['ano_id' => 5, 'nome' => 'Unidade II'],
            ['ano_id' => 5, 'nome' => 'Unidade III'],
            ['ano_id' => 5, 'nome' => 'Unidade IV'],

            ['ano_id' => 6, 'nome' => 'Unidade I'],
            ['ano_id' => 6, 'nome' => 'Unidade II'],
            ['ano_id' => 6, 'nome' => 'Unidade III'],
            ['ano_id' => 6, 'nome' => 'Unidade IV'],

            ['ano_id' => 7, 'nome' => 'Unidade I'],
            ['ano_id' => 7, 'nome' => 'Unidade II'],
            ['ano_id' => 7, 'nome' => 'Unidade III'],
            ['ano_id' => 7, 'nome' => 'Unidade IV'],

            ['ano_id' => 8, 'nome' => 'Unidade I'],
            ['ano_id' => 8, 'nome' => 'Unidade II'],
            ['ano_id' => 8, 'nome' => 'Unidade III'],
            ['ano_id' => 8, 'nome' => 'Unidade IV'],

            ['ano_id' => 9, 'nome' => 'Unidade I'],
            ['ano_id' => 9, 'nome' => 'Unidade II'],
            ['ano_id' => 9, 'nome' => 'Unidade III'],
            ['ano_id' => 9, 'nome' => 'Unidade IV'],
            
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
