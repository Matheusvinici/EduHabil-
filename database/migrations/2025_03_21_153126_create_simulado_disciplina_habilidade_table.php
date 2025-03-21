<?php

// database/migrations/xxxx_xx_xx_xxxxxx_create_simulado_disciplina_habilidade_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSimuladoDisciplinaHabilidadeTable extends Migration
{
    public function up()
    {
        Schema::create('simulado_disciplina_habilidade', function (Blueprint $table) {
            $table->id();
            $table->foreignId('simulado_id')->constrained()->onDelete('cascade');
            $table->foreignId('disciplina_id')->constrained()->onDelete('cascade');
            $table->foreignId('habilidade_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('simulado_disciplina_habilidade');
    }
}
