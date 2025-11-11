<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::create('rol_permission', function (Blueprint $table) {
        $table->id();
        $table->foreignId('rol_id')->constrained('rols')->onDelete('cascade');
        $table->foreignId('permission_id')->constrained('permissions')->onDelete('cascade');
        $table->timestamps();

        // Evitar duplicados (un rol no puede tener el mismo permiso 2 veces)
        $table->unique(['rol_id', 'permission_id']);
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rol_permission');
    }
};
