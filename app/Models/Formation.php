<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Formation extends Model
{
    protected $fillable = [
        'nom', 'description' 
    ];

    public function benevoles()
    {
        return $this->belongsToMany(Benevole::class);   
    }
}
