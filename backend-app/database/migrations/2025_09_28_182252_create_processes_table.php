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
            $table->json('sections_data');
            $table->enum('status',['started',''])->default('started');
            $table->foreignId('reporte_flash_id')->nullable()->constrained('documents')->nullOnDelete();
            $table->foreignId('diab_id')->nullable()->constrained('documents')->nullOnDelete();
            $table->foreignId('obac_id')->nullable()->constrained('documents')->nullOnDelete();
            $table->foreignId('declaracion_testigo_id')->nullable()->constrained('documents')->nullOnDelete();
            $table->foreignId('incidente_sin_lesiones_id')->nullable()->constrained('documents')->nullOnDelete();
            $table->foreignId('certificado_carabinero_id')->nullable()->constrained('documents')->nullOnDelete();
            $table->foreignId('informe_medico_id')->nullable()->constrained('documents')->nullOnDelete();
            $table->foreignId('certificado_superintendente_id')->nullable()->constrained('documents')->nullOnDelete();
            $table->foreignId('copia_libro_guardia_id')->nullable()->constrained('documents')->nullOnDelete();
            $table->foreignId('copia_libro_llamada_id')->nullable()->constrained('documents')->nullOnDelete();
            $table->foreignId('aviso_citacion_id')->nullable()->constrained('documents')->nullOnDelete();
            $table->foreignId('lista_asistencia_id')->nullable()->constrained('documents')->nullOnDelete();
            $table->foreignId('informe_ejecutivo_id')->nullable()->constrained('documents')->nullOnDelete();
            $table->foreignId('factura_id')->nullable()->constrained('documents')->nullOnDelete();
            $table->foreignId('boleta_honorario_id')->nullable()->constrained('documents')->nullOnDelete();
            $table->foreignId('boleta_medicamento_id')->nullable()->constrained('documents')->nullOnDelete();
            $table->foreignId('certificado_examen_id')->nullable()->constrained('documents')->nullOnDelete();
            $table->foreignId('boleta_traslado_id')->nullable()->constrained('documents')->nullOnDelete();
            $table->foreignId('certificado_incapacidad_medico_id')->nullable()->constrained('documents')->nullOnDelete();
            $table->foreignId('certificado_traslado_id')->nullable()->constrained('documents')->nullOnDelete();
            $table->foreignId('boleta_gasto_hospedaje_acompanante_id')->nullable()->constrained('documents')->nullOnDelete();
            $table->foreignId('boleta_gasto_alimentacion_acompanante_id')->nullable()->constrained('documents')->nullOnDelete();
            $table->foreignId('otro_gasto_id')->nullable()->constrained('documents')->nullOnDelete();
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