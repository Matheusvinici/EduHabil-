<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TutoriaCriterio extends Model
{
    // Define explicitamente a nova tabela usada
    protected $table = 'tutoria_criterios';

    // Campos que podem ser preenchidos em massa
    protected $fillable = ['categoria', 'descricao'];

    /**
     * Relacionamento: um critério pode ter várias notas associadas
     */
    public function notas(): HasMany
    {
        return $this->hasMany(NotaAvaliacao::class, 'criterio_id');
    }
}
