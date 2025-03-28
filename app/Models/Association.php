<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Association extends User
{
    protected $fillable = [
        'description', 'telephone', 'adresse', 'ville', 'code_postal', 'site_web', 'logo', 'status'
    ];

    public function evenements()
    {
        return $this->hasMany(Evenement::class);   
    }
    public function postules()
    {
        return $this->hasMany(Postule::class);   
    }
}
