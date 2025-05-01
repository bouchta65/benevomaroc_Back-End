<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class Benevole extends Model
{
    protected $fillable = [
        'user_id','domaines_action', 'types_mission', 'disponibilites', 'missions_preferrees', 'talents','niveau_etudes','metier','cv'
    ];
    
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function opportunitess()
    {
        return $this->belongsToMany(Opportunite::class);   
    }
    public function postules()
    {
        return $this->hasMany(Postule::class);   
    }

    public function certifications()
    {
        return $this->hasMany(Certification::class);
    }

}
