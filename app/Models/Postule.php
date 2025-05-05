<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Postule extends Model
{
    protected $fillable = [
        'benevole_id', 'opportunite_id', 'etat' , 'date' , 'status'
    ];

    public function benevole()
    {
        return $this->belongsTo(Benevole::class);   
    }
    public function Opportunite()
    {
        return $this->belongsTo(Opportunite::class);   
    }
}
