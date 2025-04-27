<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'escola_id',
        'turma_id',
        'codigo_acesso',
        'deficiencia',
        'email_verified_at'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Relacionamento many-to-many com escolas usando a tabela pivot personalizada
     */
    public function escola()
    {
        return $this->belongsTo(Escola::class, 'escola_id');
    }
    public function adaptacoes()
    {
        return $this->hasMany(Adaptacao::class);
    }

public function adaptacoesCount()
{
    return $this->adaptacoes()->count();
}
public function deficiencias()
{
    return $this->belongsToMany(Deficiencia::class, 'deficiencia_user');
}
public function deficiencia()
{
    return $this->belongsTo(Deficiencia::class, 'deficiencia', 'nome');
}

     public function escolas()
    {
        return $this->belongsToMany(Escola::class, 'user_escola')
                   ->using(UserEscola::class)
                   ->withTimestamps();
    }
    public function pertenceAEscola($escolaId)
{
    return $this->escola_id == $escolaId || 
           $this->escolas()->where('escolas.id', $escolaId)->exists();
}

    /**
     * Relacionamento com a tabela pivot personalizada
     */
    public function vinculosEscolas()
    {
        return $this->hasMany(UserEscola::class);
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

    // Respostas de simulado
    public function respostasSimulado()
    {
        return $this->hasMany(RespostaSimulado::class);
    }

    public function isProfessor()
    {
        return $this->role === 'professor';
    }
    public function isAee()
    {
        return $this->role === 'aee';
    }
    

    
}