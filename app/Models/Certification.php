<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Certification extends Model
{
    protected $fillable = [
        'nom', 'description' , 'date_obtention'
    ];

    public function benevoles()
    {
        return $this->belongsToMany(Benevole::class);   
    }
}
