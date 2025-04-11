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
        'aplicador_id',
        'escola_id',
        'simulado_id',
        'pergunta_id',
        'resposta',
        'correta',
        'raca',
        'tempo_resposta'
    ];

    // Relacionamento com o usuário (sem filtro)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relacionamento com o usuário, filtrando pelo 'role' de 'aluno'
    public function aluno()
    {
        return $this->belongsTo(User::class, 'user_id')->where('role', 'aluno');
    }

    // Relacionamento com o professor
    public function professor()
    {
        return $this->belongsTo(User::class, 'professor_id');
    }

    // Relacionamento com a escola
    public function escola()
    {
        return $this->belongsTo(Escola::class);
    }

    // Relacionamento com o simulado
    public function simulado()
    {
        return $this->belongsTo(Simulado::class);
    }

    // Relacionamento com a pergunta
    public function pergunta()
    {
        return $this->belongsTo(Pergunta::class);
    }
}
