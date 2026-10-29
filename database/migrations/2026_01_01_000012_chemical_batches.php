<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\Unit;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chemical_batches', function (Blueprint $table) {
            $table->id('chemical_batch_id');
            $table->foreignId('chemical_id')->constrained('chemicals', 'chemical_id')->cascadeOnDelete();
            $table->foreignId('location_id')->constrained('locations', 'location_id');
            $table->foreignId('created_by')->constrained('users', 'user_id');
            $table->string('batch_number');
            $table->string('brand_name');
            $table->decimal('quantity_per_unit', 8, 2);
            $table->integer('quantity_received');
            $table->integer('current_quantity');
            $table->date('expiration_date');
            $table->date('date_received');
            $table->string('unit')->default(Unit::KILOGRAM->value);
            $table->timestamps();

            $table->unique(['chemical_id', 'batch_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chemical_batches');
    }
};
