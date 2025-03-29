<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Postule extends Model
{
    protected $fillable = [
        'benevole_id', 'evenement_id', 'etat' , 'date'
    ];

    public function benevole()
    {
        return $this->belongsTo(Benevole::class);   
    }
    public function evenement()
    {
        return $this->belongsTo(Evenement::class);   
    }
}
