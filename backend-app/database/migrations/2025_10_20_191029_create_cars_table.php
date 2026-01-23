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
        Schema::create('cars', function (Blueprint $table) {
            $table->id();
            $table->string('name'); 
            $table->string('plate')->unique();
            $table->string('model')->nullable();
            $table->string('company');
            $table->string('status')->default('En Servicio');
            $table->string('marca')->nullable();
            $table->string('chassis_number')->nullable();
            $table->string('type')->nullable();
            $table->string('cabin')->nullable(); 
            $table->integer('mileage')->nullable();
            $table->integer('hourmeter')->nullable();
            $table->unsignedBigInteger('company_id')->nullable(); 
            $table->string('imageUrl')->nullable(); 
            $table->integer('manufacturing_year')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cars');
    }
};