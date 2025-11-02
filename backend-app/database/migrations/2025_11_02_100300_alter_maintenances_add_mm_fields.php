<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('maintenances', function (Blueprint $table) {
            if (!Schema::hasColumn('maintenances', 'car_id')) {
                $table->foreignId('car_id')->constrained('cars')->cascadeOnDelete();
            }
            if (!Schema::hasColumn('maintenances', 'supplier_id')) {
                $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->nullOnDelete();
            }
            if (!Schema::hasColumn('maintenances', 'service_date')) {
                $table->date('service_date')->nullable();
            }
            if (!Schema::hasColumn('maintenances', 'service_type')) {
                $table->string('service_type')->nullable();
            }
            if (!Schema::hasColumn('maintenances', 'location')) {
                $table->string('location')->nullable();
            }
            if (!Schema::hasColumn('maintenances', 'reported_issue')) {
                $table->text('reported_issue')->nullable();
            }
            if (!Schema::hasColumn('maintenances', 'activities_detail')) {
                $table->longText('activities_detail')->nullable();
            }
            if (!Schema::hasColumn('maintenances', 'pending_work')) {
                $table->text('pending_work')->nullable();
            }
            if (!Schema::hasColumn('maintenances', 'observations')) {
                $table->text('observations')->nullable();
            }
            if (!Schema::hasColumn('maintenances', 'inspector_name')) {
                $table->string('inspector_name')->nullable();
            }
            if (!Schema::hasColumn('maintenances', 'officer_in_charge')) {
                $table->string('officer_in_charge')->nullable();
            }
            if (!Schema::hasColumn('maintenances', 'finalized')) {
                $table->boolean('finalized')->default(false);
            }
            if (!Schema::hasColumn('maintenances', 'finalized_at')) {
                $table->timestamp('finalized_at')->nullable();
            }
            if (!Schema::hasColumn('maintenances', 'final_pdf_path')) {
                $table->string('final_pdf_path')->nullable();
            }
            if (!Schema::hasColumn('maintenances', 'created_by_user_id')) {
                $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('maintenances', 'updated_by_user_id')) {
                $table->foreignId('updated_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('maintenances', function (Blueprint $table) {
            foreach ([
                'car_id','supplier_id','service_date','service_type','location','reported_issue',
                'activities_detail','pending_work','observations','inspector_name','officer_in_charge',
                'finalized','finalized_at','final_pdf_path','created_by_user_id','updated_by_user_id'
            ] as $col) {
                if (Schema::hasColumn('maintenances', $col)) {
                    if (in_array($col, ['car_id','supplier_id','created_by_user_id','updated_by_user_id'])) {
                        $table->dropConstrainedForeignId($col);
                    } else {
                        $table->dropColumn($col);
                    }
                }
            }
        });
    }
};

