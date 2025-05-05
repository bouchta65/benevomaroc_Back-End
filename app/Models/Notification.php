<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'titre', 'message', 'date', 'heure', 'id_user'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);   
    }
}
