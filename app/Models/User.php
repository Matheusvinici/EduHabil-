<?php
namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role', 'escola_id', 'turma_id', 'cpf', 'codigo_acesso', 'deficiencia'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relacionamento com Escola
    public function escola()
    {
        return $this->belongsTo(Escola::class);
    }

    // Relacionamento com Turma (para alunos)
    public function turma()
    {
        return $this->belongsTo(Turma::class);
    }
    

    // Relacionamento com Respostas
    public function respostas()
    {
        return $this->hasMany(Resposta::class);
    }

    // Relacionamento com Professor (se for aluno)
    public function professor()
    {
        return $this->belongsTo(User::class, 'professor_id');
    }

    // Relacionamento com Alunos (se for professor)
    public function alunos()
    {
        return $this->hasMany(User::class, 'professor_id')->where('role', 'aluno');
    }

    // Relacionamento com Respostas de Simulado
    public function respostasSimulado()
    {
        return $this->hasMany(RespostaSimulado::class);
    }
}