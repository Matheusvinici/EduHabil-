<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ano extends Model
{
    use HasFactory;

    protected $fillable = ['nome'];

    public function atividades()
    {
        return $this->hasMany(Atividade::class, 'ano_id');
    }
}