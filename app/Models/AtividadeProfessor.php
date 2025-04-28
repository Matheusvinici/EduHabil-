<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AtividadeProfessor extends Model
{
    use HasFactory;
    protected $table = 'atividades_professores';

    protected $fillable = [
        'professor_id',
        'atividade_id',
    ];

    public function professor()
    {
        return $this->belongsTo(User::class, 'professor_id');
    }

    public function atividade()
    {
        return $this->belongsTo(Atividade::class)->with(['disciplinas', 'habilidades', 'ano']);
    }
}