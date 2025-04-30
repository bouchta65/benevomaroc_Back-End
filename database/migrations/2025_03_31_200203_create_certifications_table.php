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
        Schema::create('certifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('benevole_id');
            $table->unsignedBigInteger('opportunite_id');
            $table->string('image_path');
            $table->timestamps();
        
            $table->foreign('benevole_id')->references('id')->on('benevoles')->onDelete('cascade');
            $table->foreign('opportunite_id')->references('id')->on('opportunites')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certifications');
    }
};
