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
        Schema::create('processes', function (Blueprint $table) {
            $table->id();
            $table->string('bombero_name');
            $table->string('company');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('status',['started',''])->default('started');
            $table->foreignId('reporte_flash_id')->nullable();
            $table->foreignId('dau_id')->nullable();
            $table->foreignId('certificado_medico_atencion_especial_id')->nullable();
            $table->foreignId('diab_id')->nullable();
            $table->foreignId('obac_id')->nullable();
            $table->foreignId('declaracion_testigo_id')->nullable();
            $table->foreignId('incidente_sin_lesiones_id')->nullable();
            $table->foreignId('certificado_carabinero_id')->nullable();
            $table->foreignId('informe_medico_id')->nullable();
            $table->foreignId('certificado_acreditacion_voluntario_id')->nullable();
            $table->foreignId('copia_libro_guardia_id')->nullable();
            $table->foreignId('copia_libro_llamada_id')->nullable();
            $table->foreignId('aviso_citacion_id')->nullable();
            $table->foreignId('copia_lista_asistencia_id')->nullable();
            $table->foreignId('informe_ejecutivo_id')->nullable();
            $table->foreignId('factura_prestaciones_id')->nullable();
            $table->foreignId('boleta_honorario_id')->nullable();
            $table->foreignId('boleta_medicamentos_id')->nullable();
            $table->foreignId('certificado_examen_id')->nullable();
            $table->foreignId('boleta_factura_traslado_id')->nullable();
            $table->foreignId('certificado_medico_incapacidad_id')->nullable();
            $table->foreignId('certificado_medico_traslado_id')->nullable();
            $table->foreignId('boleta_gasto_acompanante_id')->nullable();
            $table->foreignId('boleta_alimentacion_acompanante_id')->nullable();
            $table->foreignId('otros_gastos_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('processes');
    }
};