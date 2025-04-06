<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Turma extends Model
{
    use HasFactory;

    protected $fillable = [
        'escola_id',
        'aplicador_id',
        'nome_turma',
        'codigo_turma'
    ];

    // Relacionamento com a escola
    public function escola()
    {
        return $this->belongsTo(Escola::class);
    }

    // Aplicador que criou a turma
    public function aplicador()
    {
        return $this->belongsTo(User::class, 'aplicador_id');
    }

    // Professores que lecionam nesta turma
    public function professores()
{
    return $this->belongsToMany(User::class, 'professor_turma', 'turma_id', 'professor_id')
                ->where('role', 'professor')
                ->withTimestamps();
}

    // Alunos da turma
    public function alunos()
    {
        return $this->hasMany(User::class, 'turma_id')
                   ->where('role', 'aluno');
    }

    // Todos usuários vinculados à turma
    public function usuarios()
    {
        return $this->hasMany(User::class, 'turma_id');
    }

    // Método para vincular professor
    public function vincularProfessor($professorId)
    {
        $this->professores()->syncWithoutDetaching($professorId);
    }
}