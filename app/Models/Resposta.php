<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resposta extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'prova_id', 'questao_id', 'resposta', 'correta'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function prova()
    {
        return $this->belongsTo(Prova::class);
    }

    public function questao()
    {
        return $this->belongsTo(Questao::class);
    }
}
