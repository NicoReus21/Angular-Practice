<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inspection_checklist_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inspection_checklist_id')
                ->constrained('inspection_checklists')
                ->cascadeOnDelete();
            $table->string('key');
            $table->string('label');
            $table->enum('value', ['yes', 'no', 'na']);
            $table->text('comment')->nullable();
            $table->timestamps();
            $table->unique(['inspection_checklist_id', 'key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inspection_checklist_items');
    }
};
