<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Turma extends Model
{
    use HasFactory;

    protected $fillable = [
        'escola_id',
        'professor_id',
        'nome_turma',
        'quantidade_alunos',
        'codigo_turma',
    ];

    // Relacionamento com Escola
    public function escola()
    {
        return $this->belongsTo(Escola::class);
    }

    // Relacionamento com Professor
    public function professor()
    {
        return $this->belongsTo(User::class, 'professor_id');
    }

    // Relacionamento com Alunos (1 turma tem muitos alunos)
    public function alunos()
    {
        return $this->hasMany(User::class, 'turma_id')->where('role', 'aluno');
    }
    public function usuarios()
{
    return $this->hasMany(User::class, 'turma_id');
}

}