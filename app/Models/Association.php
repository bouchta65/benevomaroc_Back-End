<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Association extends User
{
    protected $fillable = [
        'user_id', 'nom_association', 'date_creation', 'numero_rna_association', 'objet_social', 'site_web' ,'facebook','instagram', 'logo', 
        'carte_nationale', 'status_association'
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }
    

    public function opportunitess()
    {
        return $this->hasMany(Opportunite::class);   
    }
    public function postules()
    {
        return $this->hasMany(Postule::class);   
    }
}
