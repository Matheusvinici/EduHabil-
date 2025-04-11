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
        'name', 'email', 'password', 'role', 'escola_id', 'turma_id', 
        'codigo_acesso', 'deficiencia'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // Relacionamento com a escola
    public function escola()
    {
        return $this->belongsTo(Escola::class);
    }

    // Relacionamento com a turma (para alunos)
    public function turma()
    {
        return $this->belongsTo(Turma::class);
    }

    // Para professores - turmas que eles lecionam
    public function turmasLecionadas()
    {
        return $this->belongsToMany(Turma::class, 'professor_turma', 'professor_id', 'turma_id')
                    ->withTimestamps();
    }
    
    
    // Para aplicadores - turmas que eles criaram
    public function turmasCriadas()
    {
        return $this->hasMany(Turma::class, 'aplicador_id');
    }

    // Para professores - alunos que eles ensinam (em todas suas turmas)
    public function alunos()
    {
        return User::whereIn('turma_id', $this->turmasLecionadas()->pluck('id'))
                 ->where('role', 'aluno');
    }

    // Respostas de simulado
    public function respostasSimulado()
    {
        return $this->hasMany(RespostaSimulado::class);
    }

    // Escopo para filtrar por role
    public function scopePorRole($query, $role)
    {
        return $query->where('role', $role);
    }
}