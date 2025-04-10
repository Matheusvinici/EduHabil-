<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AvaliacaoCriterio extends Model
{
    //Tabela Pivô
    use HasFactory;

    protected $fillable = [
        'avaliacao_tutoria_id',
        'criterio_avaliacao_id',
        'nota',
    ];
}
