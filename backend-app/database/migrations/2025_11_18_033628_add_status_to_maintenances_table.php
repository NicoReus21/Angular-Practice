<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::table('maintenances', function (Blueprint $table) {
        // Por defecto será 'completed' para los antiguos, o 'draft' para nuevos
        $table->string('status')->default('completed')->after('car_id'); 
    });
}

public function down(): void
{
    Schema::table('maintenances', function (Blueprint $table) {
        $table->dropColumn('status');
    });
}
};
