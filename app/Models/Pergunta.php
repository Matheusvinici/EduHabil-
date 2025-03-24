<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pergunta extends Model
{
    use HasFactory;

    protected $fillable = [
        'ano_id',
        'disciplina_id',
        'habilidade_id',
        'enunciado',
        'alternativa_a',
        'alternativa_b',
        'alternativa_c',
        'alternativa_d',
        'resposta_correta',
        'imagem', // Campo para a imagem
    ];

    // Relacionamentos
    public function ano()
    {
        return $this->belongsTo(Ano::class);
    }

    public function disciplina()
    {
        return $this->belongsTo(Disciplina::class);
    }

    public function habilidade()
    {
        return $this->belongsTo(Habilidade::class);
    }
}