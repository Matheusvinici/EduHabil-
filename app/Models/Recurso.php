<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recurso extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'descricao',
        'como_trabalhar',
        'direcionamentos',
    ];

    // Relacionamento com Habilidade
    public function habilidade()
    {
        return $this->belongsTo(Habilidade::class);
    }

    // Relacionamento com Disciplina
    public function disciplina()
    {
        return $this->belongsTo(Disciplina::class);
    }

    // Relacionamento Many-to-Many com Deficiências
    public function deficiencias()
    {
        return $this->belongsToMany(Deficiencia::class, 'recursos_deficiencias');
    }

 
    public function caracteristicas()
{
    return $this->belongsToMany(Caracteristica::class, 'recurso_caracteristica');
}
}