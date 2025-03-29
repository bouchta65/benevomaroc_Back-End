<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evenement extends Model
{
    protected $fillable = [
        'nom', 'description', 'date', 'derniere_date_Postule' ,'Ville', 'Adress' , 'association_id' , 'categorie_id' , 'image' , 'status' , 'nb_benevole' , 'Duree' , 'Engagement_requis'
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
