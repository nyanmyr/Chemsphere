<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chemical_containers', function (Blueprint $table) {
            $table->id('chemical_container_id');
            $table->foreignId('chemical_id')->constrained('chemicals', 'chemical_id')->cascadeOnDelete();
            $table->foreignId('location_id')->constrained('locations', 'location_id');
            $table->foreignId('created_by')->constrained('users', 'user_id');
            $table->string('batch_number');
            $table->string('brand_name');
            $table->unsignedInteger('quantity_received');
            $table->unsignedInteger('current_quantity');
            $table->date('expiration_date');
            $table->date('date_received');
            $table->timestamps();
            $table->index(['chemical_id', 'expiration_date']);
            $table->index(['location_id', 'current_quantity']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chemical_containers');
    }
};
