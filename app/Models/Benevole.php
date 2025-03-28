<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Benevole extends User
{
    public function evenements()
    {
        return $this->belongsToMany(Evenement::class);   
    }
    public function postules()
    {
        return $this->hasMany(Postule::class);   
    }
}
