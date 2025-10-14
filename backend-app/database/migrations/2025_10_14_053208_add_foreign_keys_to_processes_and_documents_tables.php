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
        // Conexión: La tabla 'documents' depende de 'processes'
        Schema::table('documents', function (Blueprint $table) {
            $table->foreign('process_id')->references('id')->on('processes')->onDelete('cascade');
        });

        // Conexiones: La tabla 'processes' depende de 'documents'
        Schema::table('processes', function (Blueprint $table) {
            $table->foreign('reporte_flash_id')->references('id')->on('documents')->nullOnDelete();
            $table->foreign('diab_id')->references('id')->on('documents')->nullOnDelete();
            $table->foreign('obac_id')->references('id')->on('documents')->nullOnDelete();
            $table->foreign('declaracion_testigo_id')->references('id')->on('documents')->nullOnDelete();
            $table->foreign('incidente_sin_lesiones_id')->references('id')->on('documents')->nullOnDelete();
            $table->foreign('certificado_carabinero_id')->references('id')->on('documents')->nullOnDelete();
            $table->foreign('informe_medico_id')->references('id')->on('documents')->nullOnDelete();
            $table->foreign('dau_id')->references('id')->on('documents')->nullOnDelete();
            $table->foreign('certificado_medico_atencion_especial_id')->references('id')->on('documents')->nullOnDelete();
            $table->foreign('certificado_acreditacion_voluntario_id')->references('id')->on('documents')->nullOnDelete();
            $table->foreign('copia_libro_guardia_id')->references('id')->on('documents')->nullOnDelete();
            $table->foreign('copia_libro_llamada_id')->references('id')->on('documents')->nullOnDelete();
            $table->foreign('aviso_citacion_id')->references('id')->on('documents')->nullOnDelete();
            $table->foreign('copia_lista_asistencia_id')->references('id')->on('documents')->nullOnDelete();
            $table->foreign('informe_ejecutivo_id')->references('id')->on('documents')->nullOnDelete();
            $table->foreign('factura_prestaciones_id')->references('id')->on('documents')->nullOnDelete();
            $table->foreign('boleta_honorario_id')->references('id')->on('documents')->nullOnDelete();
            $table->foreign('boleta_medicamentos_id')->references('id')->on('documents')->nullOnDelete();
            $table->foreign('certificado_examen_id')->references('id')->on('documents')->nullOnDelete();
            $table->foreign('boleta_factura_traslado_id')->references('id')->on('documents')->nullOnDelete();
            $table->foreign('certificado_medico_incapacidad_id')->references('id')->on('documents')->nullOnDelete();
            $table->foreign('certificado_medico_traslado_id')->references('id')->on('documents')->nullOnDelete();
            $table->foreign('boleta_gasto_acompanante_id')->references('id')->on('documents')->nullOnDelete();
            $table->foreign('boleta_alimentacion_acompanante_id')->references('id')->on('documents')->nullOnDelete();
            $table->foreign('otros_gastos_id')->references('id')->on('documents')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Esto permite revertir los cambios si es necesario
        Schema::table('documents', function (Blueprint $table) {
            $table->dropForeign(['process_id']);
        });

        Schema::table('processes', function (Blueprint $table) {
            // Aquí iría la lista de dropForeign para todas las claves de arriba
            $table->dropForeign(['reporte_flash_id']);
            $table->dropForeign(['diab_id']);
            // etc...
        });
    }
};