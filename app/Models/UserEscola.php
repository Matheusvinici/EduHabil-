<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserEscola extends Model
{
    use HasFactory;

    protected $table = 'user_escola';

    protected $fillable = [
        'user_id',
        'escola_id',
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