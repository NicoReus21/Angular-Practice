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
        Schema::create('maintenances', function (Blueprint $table) {
            $table->id();
            
            // Relación con el carro (Unidad)
            $table->foreignId('car_id')->constrained('cars')->onDelete('cascade');
            $table->string('chassis_number')->nullable();
            $table->integer('mileage')->nullable(); 
            $table->string('cabin')->nullable();
            $table->string('filter_code')->nullable();
            $table->string('hourmeter')->nullable();
            $table->string('location')->nullable(); 
            
            // --- DETALLES DEL SERVICIO ---
            $table->string('service_type')->nullable();
            $table->string('inspector_name')->nullable();
            $table->date('service_date')->nullable();
            
            // --- DESCRIPCIÓN DEL TRABAJO ---
            $table->text('reported_problem')->nullable(); 
            $table->text('activities_detail')->nullable(); 
            $table->text('warnings')->nullable();
            
            // --- CIERRE Y PENDIENTES ---
            $table->text('pending_work')->nullable();
            $table->string('pending_type')->nullable()->default('Ninguno');
            $table->text('observations')->nullable();
            $table->text('car_info_annex')->nullable();
            $table->longText('inspector_signature')->nullable(); 
            $table->longText('officer_signature')->nullable(); 
            
            // --- PDF GENERADO ---
            $table->string('pdf_url')->nullable();
            $table->string('status')->default('draft'); 

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenances');
    }
};