<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class UserEscola extends Pivot
{
    use HasFactory;

    protected $table = 'user_escola';

    protected $fillable = [
        'user_id',
        'escola_id',
        // Adicione aqui outros campos extras se necessário
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function escola()
    {
        return $this->belongsTo(Escola::class);
    }
}