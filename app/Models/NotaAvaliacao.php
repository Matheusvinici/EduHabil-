<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
 
class NotaAvaliacao extends Model
{
    protected $table = 'notas_avaliacoes';

    protected $fillable = ['avaliacao_id', 'criterio_id','nota'];

    // Avaliação principal
    public function avaliacao(): BelongsTo
    {
        return $this->belongsTo(AvaliacaoTutoria::class, 'avaliacao_id');
    }

    // Critério avaliado
    public function criterio(): BelongsTo
    {
        return $this->belongsTo(CriterioAvaliacao::class, 'criterio_id');
    }
}