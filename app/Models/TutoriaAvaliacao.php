<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class TutoriaAvaliacao extends Model
{
    protected $table = 'tutoria_avaliacoes';

    protected $fillable = ['tutor_id', 'escola_id', 'data_visita', 'observacoes'];

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

    // Critérios avaliados com notas (tabela pivô)
    public function criterios(): BelongsToMany
    {
    return $this->belongsToMany(TutoriaCriterio::class, 'avaliacao_criterios', 'avaliacao_tutoria_id', 'criterio_avaliacao_id')
                ->withPivot('nota')
                ->withTimestamps();
    }

}
