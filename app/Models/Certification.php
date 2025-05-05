<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Certification extends Model
{
    protected $fillable = [
        'benevole_id','opportunite_id','image_path'
    ];

    public function benevole()
    {
        return $this->belongsTo(Benevole::class);
    }
    
    public function opportunite()
    {
        return $this->belongsTo(Opportunite::class);
    }
}
