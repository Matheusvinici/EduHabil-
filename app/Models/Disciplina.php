<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Disciplina extends Model
{
    use HasFactory;

    protected $fillable = ['nome'];

    public function habilidades()
    {
        return $this->hasMany(Habilidade::class);
    }

    public function questoes()
    {
        return $this->hasMany(Questao::class);
    }
    public function atividades()
    {
        return $this->hasMany(Atividade::class, 'disciplina_id');
    }
}