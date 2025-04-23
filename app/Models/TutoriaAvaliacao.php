<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class TutoriaAvaliacao extends Model
{
    protected $table = 'tutoria_avaliacoes';

    protected $fillable = ['tutor_id', 'escola_id', 'data_visita', 'observacoes'];

    public function tutor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tutor_id');
    }

    public function escola(): BelongsTo
    {
        return $this->belongsTo(Escola::class, 'escola_id');
    }

    public function criterios(): BelongsToMany
    {
        return $this->belongsToMany(TutoriaCriterio::class, 'avaliacao_criterios', 
            'avaliacao_tutoria_id', 'criterio_avaliacao_id')
            ->withPivot('nota')
            ->withTimestamps();
    }
    public function notas(): \Illuminate\Database\Eloquent\Relations\HasMany
{
    return $this->hasMany(NotaAvaliacao::class, 'avaliacao_tutoria_id'); // nome da foreign key correta
} 

    // Método para calcular a média dinâmica
    public function getMediaAttribute()
    {
        return $this->criterios()->avg('nota') ?? 0;
    }
}