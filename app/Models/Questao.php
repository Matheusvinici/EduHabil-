<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
       
        'enunciado',
        'alternativa_a',
        'alternativa_b',
        'alternativa_c',
        'alternativa_d',
        'resposta_correta', // A, B, C ou D
    ];

    // Relacionamento com a tabela `anos`
    public function ano(): BelongsTo
    {
        return $this->belongsTo(Ano::class, 'ano_id');
    }

    // Relacionamento com a tabela `disciplinas`
    public function disciplina(): BelongsTo
    {
        return $this->belongsTo(Disciplina::class, 'disciplina_id');
    }

    // Relacionamento com a tabela `habilidades`
    public function habilidade(): BelongsTo
    {
        return $this->belongsTo(Habilidade::class, 'habilidade_id');
    }

   

    // Relacionamento com a tabela `provas` (através da tabela pivô `questoes_provas`)
    public function provas()
    {
        return $this->belongsToMany(Prova::class, 'questoes_provas');
    }

    // Relacionamento com a tabela `respostas` (uma questão pode ter várias respostas)
    public function respostas(): HasMany
    {
        return $this->hasMany(Resposta::class, 'questao_id');
    }
}