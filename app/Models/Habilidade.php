<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Habilidade extends Model
{
    use HasFactory;

    protected $fillable = ['ano_id', 'disciplina_id', 'descricao'];

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

    public function atividades()
    {
        return $this->hasMany(Atividade::class, 'habilidade_id');
    }

    public function questoes()
    {
        return $this->hasMany(Questao::class);
    }


    public function provas(): HasMany
    {
        return $this->hasMany(Prova::class, 'habilidade_id');
    }

    /**
     * Relacionamento indireto com o modelo Resposta.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function respostas(): HasManyThrough
    {
        return $this->hasManyThrough(
            Resposta::class, // Modelo final (Resposta)
            Questao::class,  // Modelo intermediário (Questao)
            'habilidade_id', // Chave estrangeira no modelo intermediário (Questao)
            'questao_id',    // Chave estrangeira no modelo final (Resposta)
            'id',            // Chave local no modelo atual (Habilidade)
            'id'             // Chave local no modelo intermediário (Questao)
        );
    }


    
    // Relacionamento com a tabela Simulado
    public function simulados()
    {
        return $this->hasMany(Simulado::class);
    }
}

