<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name', 'email', 'password', 'role', 'escola_id', 'cpf', 'matricula', 'codigo_acesso'

    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function escola()
    {
        return $this->belongsTo(Escola::class);
    }

 
            // No modelo User.php
        public function turma()
        {
            return $this->belongsTo(Turma::class, 'turma_id');
        }

    public function respostas()
    {
        return $this->hasMany(Resposta::class);
    }
            public function professor()
        {
            return $this->belongsTo(User::class, 'professor_id');
        }

        public function alunos()
        {
            return $this->hasMany(User::class, 'professor_id');
        }
        
   
}
