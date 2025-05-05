<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Opportunite extends Model
{
    protected $fillable = [
        'titre', 'description', 'date', 'derniere_date_postule' ,'ville' , 'pays' , 'adresse' , 'association_id' , 'categorie_id' , 'image' , 'status' , 'nb_benevole' , 'duree' , 'engagement_requis','missions_principales','competences'
    ];

    public function association()
    {
        return $this->belongsTo(Association::class);   
    }
    public function categorie()
    {
        return $this->belongsTo(Categorie::class);   
    }
    public function benevoles()
    {
        return $this->belongsToMany(Benevole::class);   
    }
    public function postules()
    {
        return $this->hasMany(Postule::class);   
    }
}
