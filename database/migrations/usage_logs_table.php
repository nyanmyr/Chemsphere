<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usage_logs', function (Blueprint $table) {
            $table->id('usage_log_id');
            $table->enum('item_type', ['equipment', 'chemical']);
            $table->decimal('quantity_used', $precision = 10, $scale = 3);
            $table->date('usage_log_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usage_logs');
    }
};
