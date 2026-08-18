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
            $table->foreignId('user_id')->constrained('users')->references('user_id');
            $table->foreignId('location_id')->constrained('locations')->references('location_id');
            $table->enum('item_type', ['equipment', 'chemical']);
            $table->unsignedBigInteger('item_id');
            $table->decimal('quantity_used', $precision = 10, $scale = 3);
            $table->decimal('quantity_remaining', $precision = 10, $scale = 3);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usage_logs');
    }
};
