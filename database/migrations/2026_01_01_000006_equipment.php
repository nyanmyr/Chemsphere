<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\EquipmentStatus;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('equipment', function (Blueprint $table) {
            $table->id('equipment_id');
            $table->foreignId('location_id')->constrained('locations')->references('location_id');
            $table->foreignId('created_by')->constrained('users')->references('user_id');
            $table->string('equipment_name');
            $table->string('model');
            $table->string('serial_id');
            $table->string('status')->default(EquipmentStatus::AVAILABLE->value);
            $table->date('date_received');
            $table->date('warranty_expiration');
            $table->date('last_maintenance');
            $table->date('next_maintenance');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipment');
    }
};
