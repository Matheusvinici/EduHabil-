<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GabaritoTemplate extends Model
{
    protected $fillable = [
        'nome',
        'descricao',
        'quantidade_questoes',
        'template_json' // Estrutura do gabarito (posições das respostas, etc.)
    ];
    
    protected $casts = [
        'template_json' => 'array'
    ];
}