<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Adaptacao extends Model
{
    use HasFactory;

    protected $table = 'adaptacoes';

    protected $fillable = ['recurso_id', 'user_id'];

    public function deficiencias()
    {
        return $this->belongsToMany(Deficiencia::class, 'adaptacao_deficiencia')
            ->withTimestamps();
    }

    public function caracteristicas()
    {
        return $this->belongsToMany(Caracteristica::class, 'adaptacao_caracteristica')
            ->withTimestamps();
    }

    
    public function recurso()
    {
        return $this->belongsTo(Recurso::class)->withDefault([
            'nome' => 'Recurso não encontrado',
            'descricao' => '',
            'como_trabalhar' => '',
            'direcionamentos' => ''
        ]);
    }
    public function criador()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}