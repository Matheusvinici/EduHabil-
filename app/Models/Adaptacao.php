<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Adaptacao extends Model
{
    use HasFactory;

    protected $table = 'adaptacoes';

    protected $fillable = ['recurso_id'];

    // Relacionamento Many-to-Many com Deficiências
    public function deficiencias()
    {
        return $this->belongsToMany(Deficiencia::class, 'adaptacao_deficiencia');
    }

    // Relacionamento Many-to-Many com Características
    public function caracteristicas()
    {
        return $this->belongsToMany(Caracteristica::class, 'adaptacao_caracteristica');
    }

    // Relacionamento com Recurso
    public function recurso()
    {
        return $this->belongsTo(Recurso::class);
    }
}