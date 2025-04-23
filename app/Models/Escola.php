<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Escola extends Model
{
    use HasFactory;

    // Campos que podem ser preenchidos em massa
    protected $fillable = [
        'nome',
        'endereco',
        'telefone',
        'codigo_escola'
    ];
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_escola');
    }
    // No modelo Escola.php
public function professores()
{
    return $this->belongsToMany(User::class, 'user_escola', 'escola_id', 'user_id')
                ->where('role', 'professor');
}
       // Remova o relacionamento many-to-many e adicione:
        public function alunos()
        {
            return $this->hasMany(User::class, 'escola_id')->where('role', 'aluno');
        }
    // Relacionamento com o modelo Turma
    public function turmas(): HasMany
    {
        return $this->hasMany(Turma::class);
    }

    // Relacionamento com o modelo Prova
    public function provas(): HasMany
    {
        return $this->hasMany(Prova::class, 'escola_id');
    }
    public function avaliacoes(): HasMany
    {
        return $this->hasMany(TutoriaAvaliacao::class, 'escola_id');
    }
    public function notas(): HasMany
    {
        return $this->hasMany(NotaAvaliacao::class, 'escola_id'); // ✅ Certo!
    }
    
}