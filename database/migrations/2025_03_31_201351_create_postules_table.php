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
        Schema::create('postules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('benevole_id')->constrained('benevoles')->onDelete('cascade');
            $table->foreignId('opportunite_id')->constrained('opportunites')->onDelete('cascade');
            $table->enum('etat', ['accepté', 'refusé', 'en attente'])->default('en attente'); 
            $table->date('date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('postules');
    }
};
