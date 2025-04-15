<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class TutoriaCriterio extends Model
{
    protected $table = 'tutoria_criterios';

    protected $fillable = ['categoria', 'descricao'];

    public function avaliacoes(): BelongsToMany
    {
        return $this->belongsToMany(TutoriaAvaliacao::class, 'avaliacao_criterios', 'criterio_avaliacao_id', 'avaliacao_tutoria_id')
                    ->withPivot('nota')
                    ->withTimestamps();
    }
}
