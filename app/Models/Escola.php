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
}