<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Atividade extends Model
{
    use HasFactory;
    protected $table = 'atividades';

    protected $fillable = [
        'disciplina_id',
        'ano_id',
        'habilidade_id',
        'titulo',
        'objetivo',
        'metodologia',
        'materiais',
        'resultados_esperados',
    ];

    // Relacionamento com disciplina
    public function disciplina()
    {
        return $this->belongsTo(Disciplina::class, 'disciplina_id');
    }

    // Relacionamento com ano
    public function ano()
    {
        return $this->belongsTo(Ano::class, 'ano_id');
    }

    // Relacionamento com habilidade
    public function habilidade()
    {
        return $this->belongsTo(Habilidade::class, 'habilidade_id');
    }

  
}