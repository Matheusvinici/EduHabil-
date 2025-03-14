<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Habilidade extends Model
{
    use HasFactory;

    protected $fillable = ['ano_id', 'disciplina_id', 'descricao'];

    public function ano()
    {
        return $this->belongsTo(Ano::class);
    }

    public function disciplina()
    {
        return $this->belongsTo(Disciplina::class);
    }

    public function atividades()
    {
        return $this->hasMany(Atividade::class, 'habilidade_id');
    }

    public function questoes()
    {
        return $this->hasMany(Questao::class);
    }
}