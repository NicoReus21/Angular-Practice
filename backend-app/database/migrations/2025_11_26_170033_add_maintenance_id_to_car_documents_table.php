<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('car_documents', function (Blueprint $table) {
            $table->foreignId('maintenance_id')
                ->nullable()
                ->constrained('maintenances')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('car_documents', function (Blueprint $table) {
            $table->dropForeign(['maintenance_id']);
            $table->dropColumn('maintenance_id');
        });
    }
};
