<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
 

    public function up(): void
    {
        Schema::create('opportunites', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('titre');
            $table->text('description');
            $table->date('date');
            $table->date('derniere_date_postule');  
            $table->string('ville');
            $table->string('adresse');  
            $table->foreignId('association_id')->constrained('associations')->onDelete('cascade');
            $table->foreignId('categorie_id')->constrained('categories')->onDelete('cascade');  
            $table->string('image')->nullable();  
            $table->enum('status', ['actif', 'en attente', 'fermé'])->default('en attente');
            $table->enum('type', ['Social', 'Environnement', 'Santé', 'Éducation', 'Culture', 'Sport', 'Technologie', 'Économie' , 'Autre']);
            $table->integer('nb_benevole');
            $table->string('duree');  
            $table->string('engagement_requis');  
            $table->text('missions_principales');  
            $table->text('competences');  
            $table->string('pays');  
        });
}

    public function down(): void
    {
        Schema::dropIfExists('opportunites');
    }
};
