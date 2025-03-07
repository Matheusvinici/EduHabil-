<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('anos', function (Blueprint $table) {
            $table->id();
            $table->string('nome')->unique(); // Ex: "1º ano", "2º ano", ..., "9º ano"
            $table->timestamps();
        });

        // Inserindo anos do 1º ao 9º
        $anos = [
            '1º ano', '2º ano', '3º ano', '4º ano', '5º ano',
            '6º ano', '7º ano', '8º ano', '9º ano'
        ];

        foreach ($anos as $ano) {
            DB::table('anos')->insert(['nome' => $ano]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('anos');
    }
};
