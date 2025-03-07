<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Questao extends Model
{
    use HasFactory;

    // Nome da tabela (opcional, pois o Laravel já infere o nome da tabela)
    protected $table = 'questoes';

    // Campos que podem ser preenchidos em massa
    protected $fillable = [
        'ano_id',
        'disciplina_id',
        'habilidade_id',
        'unidade_id', // Adicionado
        'enunciado',
        'alternativa_a', // Adicionado
        'alternativa_b', // Adicionado
        'alternativa_c', // Adicionado
        'alternativa_d', // Adicionado
        'resposta_correta', // A, B, C ou D
    ];

    // Relacionamento com a tabela `anos`
    public function ano()
    {
        return $this->belongsTo(Ano::class, 'ano_id');
    }

    // Relacionamento com a tabela `disciplinas`
    public function disciplina()
    {
        return $this->belongsTo(Disciplina::class, 'disciplina_id');
    }

    // Relacionamento com a tabela `habilidades`
    public function habilidade()
    {
        return $this->belongsTo(Habilidade::class, 'habilidade_id');
    }

    // Relacionamento com a tabela `unidades` (adicionado)
    public function unidade()
    {
        return $this->belongsTo(Unidade::class, 'unidade_id');
    }

    // Relacionamento com a tabela `provas` (através da tabela pivô `questoes_provas`)
    public function provas()
    {
        return $this->belongsToMany(Prova::class, 'questoes_provas');
    }

    // Relacionamento com a tabela `respostas` (uma questão pode ter várias respostas)
    public function respostas()
    {
        return $this->hasMany(Resposta::class, 'questao_id');
    }
}