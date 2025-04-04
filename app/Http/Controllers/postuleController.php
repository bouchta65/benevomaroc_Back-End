<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\Postule;
use App\Models\Benevole;


class postuleController extends Controller
{

    public function hasAlreadyPostulated($event_id){
        $benevole_id = Benevole::where('user_id',Auth::user()->id)->first();
        $postuled = Postule::where('benevole_id',$benevole_id->id)->where('evenement_id',$event_id)->first();
        return $postuled ? true : false;
    }

   
}
