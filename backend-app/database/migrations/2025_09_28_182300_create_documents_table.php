<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('process_id')->constrained()->onDelete('cascade');
            $table->string('section_title');
            $table->enum('step', ['requerimiento_operativo','antecedentes_generales','cuerpo_de_bomberos','prestaciones_medicas','otros']);
            $table->string('file_name');
            $table->string('file_path');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();
        });
    }
    
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};