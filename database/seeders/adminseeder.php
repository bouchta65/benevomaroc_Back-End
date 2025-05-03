<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class adminseeder extends Seeder
{
    public function run()
    {
        $user = User::create([
            'civilite' => 'M.',
            'prenom' => 'Mohamed',
            'nom' => 'Bouchta',
            'email' => 'bouchtamohamed01@gmail.com',
            'password' => Hash::make('password123A1@'),
            'image' => 'http://127.0.0.1:8000/storage/admin/bouchta_mohamed/mOwPq7qz3unXwW5zAVJfHcltDONMtRhOViArLdqm.png',
            'cin' => 'JF64846',
            'adresse' => '1, Rue de l’Admin',
            'date_naissance' => '1990-01-01',
            'ville' => 'Safi',
            'telephone_1' => '0762389431',
            'telephone_2' => '0611111111',
            'role' => 'admin'
        ]);

        Admin::create([
            'user_id' => $user->id
        ]);
    }
}
