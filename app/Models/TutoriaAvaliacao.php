<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TutoriaAvaliacao extends Model
{
    protected $table = 'tutoria_avaliacoes_tutoria';

    protected $fillable = ['tutor_id', 'escola_id', 'data_visita','observacoes'];

    // Tutor que fez a avaliação
    public function tutor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tutor_id');
    }

    // Escola avaliada
    public function escola(): BelongsTo
    {
        return $this->belongsTo(Escola::class, 'escola_id');
    }



    // Notas dos critérios
    public function notas(): HasMany
    {
        return $this->hasMany(NotaAvaliacao::class, 'avaliacao_id');
    }
}
