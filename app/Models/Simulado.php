<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Simulado extends Model
{
    use HasFactory;

    protected $table = 'simulados';
    protected $fillable = [
        'nome',
        'descricao',
        'ano_id',
    ];

    public function perguntas()
    {
        return $this->belongsToMany(Pergunta::class, 'perguntas_simulados');
    }

    public function respostas()
    {
        return $this->hasMany(RespostaSimulado::class);
    }

    public function ano()
    {
        return $this->belongsTo(Ano::class);
    }
}