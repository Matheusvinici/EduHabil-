<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotaAvaliacao extends Model
{
    protected $table = 'avaliacao_criterios'; // Usando a tabela existente

    protected $fillable = ['avaliacao_id', 'criterio_id', 'nota'];

    public function avaliacao(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(TutoriaAvaliacao::class, 'avaliacao_tutoria_id');
    }
    

    public function criterio(): BelongsTo
    {
        return $this->belongsTo(TutoriaCriterio::class, 'criterio_id');
    }
}