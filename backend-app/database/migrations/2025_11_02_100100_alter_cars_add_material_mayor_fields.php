<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cars', function (Blueprint $table) {
            // Company association
            if (!Schema::hasColumn('cars', 'company_id')) {
                $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();
            }
            // Additional attributes per requirements
            if (!Schema::hasColumn('cars', 'chassis_number')) {
                $table->string('chassis_number')->nullable();
            }
            if (!Schema::hasColumn('cars', 'type')) {
                $table->string('type')->nullable(); // camion, camioneta, carro_arrastre, moto_agua, ATB, BT
            }
            if (!Schema::hasColumn('cars', 'cabin')) {
                $table->string('cabin')->nullable();
            }
            if (!Schema::hasColumn('cars', 'mileage')) {
                $table->unsignedBigInteger('mileage')->default(0);
            }
            if (!Schema::hasColumn('cars', 'hourmeter')) {
                $table->unsignedBigInteger('hourmeter')->default(0);
            }
            if (!Schema::hasColumn('cars', 'status')) {
                $table->enum('status', ['active','maintenance','urgent','retired'])->default('active');
            }
            // Ensure patente is unique for identification
            try {
                $table->unique('patente');
            } catch (\Throwable $e) {
                // ignore if already indexed/unique
            }
        });
    }

    public function down(): void
    {
        Schema::table('cars', function (Blueprint $table) {
            if (Schema::hasColumn('cars', 'company_id')) {
                $table->dropConstrainedForeignId('company_id');
            }
            foreach (['chassis_number','type','cabin','mileage','hourmeter','status'] as $col) {
                if (Schema::hasColumn('cars', $col)) {
                    $table->dropColumn($col);
                }
            }
            // drop unique index if exists
            try { $table->dropUnique(['patente']); } catch (\Throwable $e) {}
        });
    }
};

