<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RespostaSimulado extends Model
{
    use HasFactory;

    protected $table = 'respostas_simulados';

    protected $fillable = [
        'user_id',
        'professor_id',
        'escola_id',
        'simulado_id',
        'pergunta_id',
        'resposta',
        'correta',
         'raca',
         'tempo_resposta'
        ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function professor()
    {
        return $this->belongsTo(User::class, 'professor_id');
    }

    public function escola()
    {
        return $this->belongsTo(Escola::class);
    }

    public function simulado()
    {
        return $this->belongsTo(Simulado::class);
    }

    public function pergunta()
    {
        return $this->belongsTo(Pergunta::class);
    }
}