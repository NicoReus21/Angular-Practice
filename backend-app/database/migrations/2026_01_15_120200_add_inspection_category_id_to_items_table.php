<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inspection_checklist_items', function (Blueprint $table) {
            $table->foreignId('inspection_category_id')
                ->nullable()
                ->constrained('inspection_categories')
                ->nullOnDelete()
                ->after('inspection_checklist_id');
            $table->index('inspection_category_id', 'inspection_checklist_items_category_idx');
            $table->unique(
                ['inspection_checklist_id', 'inspection_category_id'],
                'inspection_checklist_items_checklist_category_unique'
            );
        });

        if (Schema::hasColumn('inspection_checklist_items', 'key')) {
            $categories = DB::table('inspection_categories')->get()->keyBy('key');
            if ($categories->isNotEmpty()) {
                foreach ($categories as $key => $category) {
                    DB::table('inspection_checklist_items')
                        ->whereNull('inspection_category_id')
                        ->where('key', $key)
                        ->update([
                            'inspection_category_id' => $category->id,
                            'label' => $category->label,
                        ]);
                }
            }
        }
    }

    public function down(): void
    {
        Schema::table('inspection_checklist_items', function (Blueprint $table) {
            $table->dropUnique('inspection_checklist_items_checklist_category_unique');
            $table->dropIndex('inspection_checklist_items_category_idx');
            $table->dropConstrainedForeignId('inspection_category_id');
        });
    }
};
