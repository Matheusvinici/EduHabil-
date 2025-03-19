<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deficiencia extends Model
{
    use HasFactory;

    protected $fillable = ['nome', 'descricao'];

    // Relacionamento com Caracteristicas
    public function caracteristicas()
    {
        return $this->hasMany(Caracteristica::class);
    }

    // Relacionamento Many-to-Many com Recursos
    public function recursos()
    {
        return $this->belongsToMany(Recurso::class, 'recurso_deficiencia');
    }
}