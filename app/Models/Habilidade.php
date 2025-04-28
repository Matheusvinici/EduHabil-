<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Habilidade extends Model
{
    use HasFactory;

    protected $fillable = ['ano_id', 'disciplina_id', 'codigo', 'descricao'];

    public function ano()
    {
        return $this->belongsTo(Ano::class);
    }

    public function perguntas()
    {
        return $this->hasMany(Pergunta::class);
    }

    public function disciplina()
    {
        return $this->belongsTo(Disciplina::class);
    }

    // RELACIONAMENTO ERRADO - Deve ser many-to-many com atividades
    public function atividades()
    {
        return $this->belongsToMany(Atividade::class, 'atividade_habilidade')
                   ->withTimestamps();
    }

    public function questoes()
    {
        return $this->hasMany(Questao::class);
    }

    public function provas(): HasMany
    {
        return $this->hasMany(Prova::class, 'habilidade_id');
    }

    public function respostas(): HasManyThrough
    {
        return $this->hasManyThrough(
            Resposta::class,
            Questao::class,
            'habilidade_id',
            'questao_id',
            'id',
            'id'
        );
    }

    public function simulados()
    {
        return $this->hasMany(Simulado::class);
    }
}