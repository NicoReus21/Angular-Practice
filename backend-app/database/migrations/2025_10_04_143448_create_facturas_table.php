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
        Schema::create('facturas', function (Blueprint $table) {
            $table->id();
            $table->integer('documento_id');
            $table->string('numero_factura', 50);
            $table->date('fecha_emision');
            $table->decimal('monto_total', 10, 2);
            $table->decimal('impuesto', 10, 2);
            $table->string('estado', 20);
            $table->decimal('monto_neto', 10, 2);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facturas');
    }
};
