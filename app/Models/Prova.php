<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Prova extends Model
{
    use HasFactory;

    // Campos que podem ser preenchidos em massa
    protected $fillable = [
        'ano_id',
        'user_id',
        'disciplina_id',
                'habilidade_id',
        'nome',
        'data',
        'observacoes'
    ];

    // Relacionamento com o modelo Ano
    public function ano(): BelongsTo
    {
        return $this->belongsTo(Ano::class);
    }

    // Relacionamento com o modelo Disciplina
    public function disciplina(): BelongsTo
    {
        return $this->belongsTo(Disciplina::class);
    }

  

    // Relacionamento com o modelo Habilidade
    public function habilidade(): BelongsTo
    {
        return $this->belongsTo(Habilidade::class);
    }

    // Relacionamento muitos-para-muitos com o modelo Questao
    public function questoes(): BelongsToMany
    {
        return $this->belongsToMany(Questao::class, 'questoes_provas', 'prova_id', 'questao_id');
    }

    // Relacionamento um-para-muitos com o modelo Resposta
    public function respostas(): HasMany
    {
        return $this->hasMany(Resposta::class);
    }

    // Relacionamento com o professor (alias para o relacionamento user)
    public function professor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}