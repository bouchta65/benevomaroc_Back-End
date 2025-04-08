<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Benevole;
use App\Models\Association;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;

class ProfileController extends Controller
{
    public function getProfile()
    {
        try {
            $user = Auth::user();
            if ($user->role == 'benevole') {
                $benevole = Benevole::join('users', 'benevoles.user_id', '=', 'users.id')->where('users.id', $user->id)->select('benevoles.*', 'users.*') ->first();     
                    return response()->json(["benevole" => $benevole], 200);
            } elseif ($user->role == 'association') {
                $association = Association::join('users','associations.user_id','=','users.id')->where('users.id',$user->id)->select('associations.*','users.*')->first();
                return response()->json(["association" => $association], 200);
            } elseif ($user->role == 'admin') {
                $admin = User::where('id', $user->id)->first();
                return response()->json(["admin" => $admin], 200);
            }
            return response()->json(["message" => "Profil introuvable"], 404);
        } catch (\Exception $e) {
            return response()->json(["message" => "Erreur", "error" => $e->getMessage()], 500);
        }
    }

    
    
    





}
