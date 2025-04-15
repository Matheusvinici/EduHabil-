<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AvaliacaoCriterio extends Model
{
    protected $table = 'avaliacao_criterios';

    protected $fillable = ['avaliacao_tutoria_id', 'criterio_avaliacao_id', 'nota'];

    public function avaliacao(): BelongsTo
    {
        return $this->belongsTo(TutoriaAvaliacao::class, 'avaliacao_tutoria_id');
    }

    public function criterio(): BelongsTo
    {
        return $this->belongsTo(TutoriaCriterio::class, 'criterio_avaliacao_id');
    }
}
