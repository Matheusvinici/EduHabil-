<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TutoriaAcompanhamento extends Model
{
    protected $table = 'tutoria_acompanhamentos';

    protected $fillable = [
        'avaliacao_id',
        'criterio_id',
        'prioridade', // alta, media, baixa
        'acao_melhoria',
        'responsavel_id',
        'prazo',
        'status', // pendente, em_andamento, concluido
        'observacoes'
    ];

    protected $casts = [
        'prazo' => 'date',
    ];

    public function avaliacao(): BelongsTo
    {
        return $this->belongsTo(TutoriaAvaliacao::class, 'avaliacao_id');
    }

    public function criterio(): BelongsTo
    {
        return $this->belongsTo(TutoriaCriterio::class, 'criterio_id');
    }

    public function responsavel(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsavel_id');
    }
}