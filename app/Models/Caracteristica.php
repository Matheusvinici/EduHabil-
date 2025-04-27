<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Caracteristica extends Model
{
    use HasFactory;

    protected $fillable = ['deficiencia_id', 'nome', 'descricao'];

    // Relacionamento com Deficiencia
    public function deficiencia()
    {
        return $this->belongsTo(Deficiencia::class);
    }

    public function recursos()
    {
        return $this->belongsToMany(Recurso::class, 'recurso_caracteristica');
    }

    // Adicione este novo relacionamento
    public function adaptacoes()
    {
        return $this->belongsToMany(Adaptacao::class, 'adaptacao_caracteristica');
    }
}