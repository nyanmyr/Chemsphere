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
        Schema::create('alerts', function (Blueprint $table) {
            $table->id('alert_id');
            $table->foreignId('chemical_id')->constrained('chemicals');
            $table->foreignId('equipment_id')->constrained('equipment');
            $table->enum('item_type', ['equipment', 'chemical']);
            $table->text('message')->nullable();
            $table->date('alert_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alerts');
    }
};
