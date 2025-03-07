<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aluno extends Model
{
    use HasFactory;

    protected $fillable = ['turma_id', 'codigo_acesso', 'nome', 'numero_chamada'];

    public function turma()
    {
        return $this->belongsTo(Turma::class);
    }
}