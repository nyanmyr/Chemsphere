<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('equipment', function (Blueprint $table) {
            $table->id('equipment_id');
            $table->foreignId('unit_id')->constrained('units')->references('unit_id');
            $table->foreignId('location_id')->constrained('locations')->references('location_id');
            $table->string('equipment_name');
            $table->enum(
                'status',
                [
                    'available',
                    'unavailable',
                    'broken',
                    'under maintenance'
                ]
            )->default('unavailable');
            $table->decimal('quantity', $precision = 10, $scale = 3);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment');
    }
};
