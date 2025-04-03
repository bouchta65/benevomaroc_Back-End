<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('associations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('fonction_occupee');
            $table->string('nom_association');
            $table->string('sigle_association');
            $table->string('numero_rna_association')->unique();
            $table->text('objet_social');
            $table->string('site_web')->nullable();
            $table->string('logo');
            $table->text('presentation_association')->nullable();
            $table->text('principales_reussites')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('associations');
    }
};
