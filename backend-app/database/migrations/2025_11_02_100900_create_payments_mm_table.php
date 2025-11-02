<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments_mm', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices_mm')->cascadeOnDelete();
            $table->date('paid_at')->nullable();
            $table->decimal('amount', 12, 2)->default(0);
            $table->string('method')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments_mm');
    }
};

