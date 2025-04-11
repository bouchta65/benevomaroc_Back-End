<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Association extends User
{
    protected $fillable = [
        'user_id','fonction_occupee', 'nom_association', 'sigle_association', 'numero_rna_association', 'objet_social', 'site_web', 'logo', 
        'presentation_association', 'principales_reussites'
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
