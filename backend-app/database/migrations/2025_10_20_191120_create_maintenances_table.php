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
        // Creamos la tabla 'maintenances' para guardar los reportes
        Schema::create('maintenances', function (Blueprint $table) {
            $table->id();
            
            // Relación con el carro (Unidad)
            $table->foreignId('car_id')->constrained('cars')->onDelete('cascade');

            // --- Campos de tu formulario 'create-report' ---
            // (Los campos 'marca', 'patente', 'compania' ya están en el carro)
            $table->string('chassis_number')->nullable();
            $table->integer('mileage')->nullable();
            $table->string('cabin')->nullable();
            $table->string('filter_code')->nullable();
            $table->string('hourmeter')->nullable();
            $table->text('warnings')->nullable();
            $table->string('service_type'); // Mantención Preventiva, etc.
            $table->string('inspector_name'); // Inspector a Cargo
            $table->date('service_date'); // Fecha de Realización
            $table->string('location')->nullable(); // Ubicación del Equipo
            $table->text('reported_problem');
            $table->text('activities_detail');
            $table->text('pending_work')->nullable();
            $table->string('pending_type')->nullable()->default('Ninguno');
            $table->text('observations')->nullable();
            $table->string('inspector_signature'); // Firma (guardado como texto, ej: nombre)
            $table->string('officer_signature'); // Firma (guardado como texto, ej: nombre)
            $table->text('car_info_annex')->nullable();
            
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