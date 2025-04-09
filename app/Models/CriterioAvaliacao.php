<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CriterioAvaliacao extends Model
{
    protected $table = 'criterios_avaliacao';

    protected $fillable = ['categoria', 'descricao'];

    // Relação com notas de avaliação
    public function notas(): HasMany
    {
        return $this->hasMany(NotaAvaliacao::class, 'criterio_id');
    }
}