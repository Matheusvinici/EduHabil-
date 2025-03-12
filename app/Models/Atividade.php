<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Atividade extends Model
{
    use HasFactory;

    protected $fillable = [
        'habilidade_id',
        'ano_id',
        'disciplina_id',
        'titulo',
        'dica_ludica',
        'dinamica',
        'plano_aula',
        'duracao',
        'materiais',
        'passo_a_passo',
        'adaptacoes',
    ];

    // Relacionamento com Habilidade
    public function habilidade()
    {
        return $this->belongsTo(Habilidade::class);
    }

    // Relacionamento com Ano
    public function ano()
    {
        return $this->belongsTo(Ano::class);
    }

    // Relacionamento com Disciplina
    public function disciplina()
    {
        return $this->belongsTo(Disciplina::class);
    }
}