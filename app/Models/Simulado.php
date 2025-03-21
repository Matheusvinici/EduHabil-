<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Simulado extends Model
    {
        protected $fillable = [
            'user_id',       // Adicionando 'user_id' para permitir a atribuição em massa
            'ano_id',
            'disciplina_id',
            'nome',
            'data',
            'observacoes',
        ];

        public function disciplinas()
        {
            return $this->belongsToMany(Disciplina::class)->withPivot('habilidade_id');
        }

        public function questoes()
        {
            return $this->belongsToMany(Questao::class);
        }
    }
