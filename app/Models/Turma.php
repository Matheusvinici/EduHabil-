<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Turma extends Model
{
    use HasFactory;

    protected $fillable = [
        'escola_id',
        'professor_id',
        'nome_turma', // Certifique-se de que está aqui
        'quantidade_alunos',
        'codigo_turma',
    ];
    public function escola()
    {
        return $this->belongsTo(Escola::class);
    }

// No modelo Turma.php
public function professor()
{
    return $this->belongsTo(User::class, 'professor_id');
}

  
    public function alunos()
{
    return $this->hasMany(User::class, 'turma_id')->where('role', 'aluno');
}

}