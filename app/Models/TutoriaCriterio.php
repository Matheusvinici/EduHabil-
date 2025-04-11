<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class TutoriaCriterio extends Model
{
    // Define explicitamente a nova tabela usada
    protected $table = 'tutoria_criterios';

    // Campos que podem ser preenchidos em massa
    protected $fillable = ['categoria', 'descricao'];

    /**
     * Relacionamento: um critério pode aparecer em várias avaliações
     */
    public function avaliacoes(): BelongsToMany
    {
        return $this->belongsToMany(TutoriaAvaliacao::class, 'avaliacao_criterios', 'criterio_avaliacao_id', 'avaliacao_tutoria_id')
                    ->withPivot('nota')
                    ->withTimestamps();
    }
}
