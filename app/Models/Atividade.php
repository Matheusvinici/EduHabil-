<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Atividade extends Model
{
    use HasFactory;

    protected $fillable = [
        'ano_id',
        'titulo',
        'objetivo',
        'metodologia',
        'materiais',
        'resultados_esperados',
        'links_sugestoes'

    ];

    public function disciplinas()
    {
        return $this->belongsToMany(Disciplina::class, 'atividade_disciplina')
                   ->withTimestamps();
    }
   

    public function habilidades()
    {
        return $this->belongsToMany(Habilidade::class, 'atividade_habilidade')
                   ->withTimestamps();
    }

    public function ano()
    {
        return $this->belongsTo(Ano::class);
    }
}