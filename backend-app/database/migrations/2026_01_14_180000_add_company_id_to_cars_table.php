<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cars', function (Blueprint $table) {
            if (!Schema::hasColumn('cars', 'company_id')) {
                $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();
            }
        });

        if (Schema::hasTable('cars') && Schema::hasTable('companies')) {
            $names = DB::table('cars')
                ->whereNotNull('company')
                ->select('company')
                ->distinct()
                ->pluck('company');

            foreach ($names as $name) {
                if (!$name) {
                    continue;
                }

                $companyId = DB::table('companies')->where('name', $name)->value('id');
                if (!$companyId) {
                    $companyId = DB::table('companies')->insertGetId([
                        'name' => $name,
                        'code' => Str::slug($name, '-'),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                } else {
                    $code = DB::table('companies')->where('id', $companyId)->value('code');
                    if (!$code) {
                        DB::table('companies')->where('id', $companyId)->update([
                            'code' => Str::slug($name, '-'),
                            'updated_at' => now(),
                        ]);
                    }
                }

                DB::table('cars')
                    ->where('company', $name)
                    ->update(['company_id' => $companyId]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('cars', function (Blueprint $table) {
            if (Schema::hasColumn('cars', 'company_id')) {
                $table->dropForeign(['company_id']);
                $table->dropColumn('company_id');
            }
        });
    }
};
