<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Resposta extends Model
{
    use HasFactory;

    // Campos que podem ser preenchidos em massa
    protected $fillable = [
        'user_id',
        'prova_id',
        'questao_id',
        'resposta',
        'correta'
    ];

    // Relacionamento com o usuário (aluno que respondeu)
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Relacionamento com a prova
    public function prova(): BelongsTo
    {
        return $this->belongsTo(Prova::class);
    }

    // Relacionamento com a questão
    public function questao(): BelongsTo
    {
        return $this->belongsTo(Questao::class);
    }
}