<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\SafetyClass;
use App\GHSSymbol;
use App\Unit;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chemicals', function (Blueprint $table) {
            $table->id('chemical_id');
            $table->foreignId('location_id')->constrained('locations')->references('location_id');
            $table->foreignId('created_by')->constrained('users')->references('user_id');
            $table->string('chemical_name');
            $table->string('batch_number');
            $table->string('brand_name');
            $table->decimal('volume_per_unit', $precision = 10, $scale = 3);
            $table->decimal('initial_quantity', $precision = 10, $scale = 3);
            $table->decimal('current_quantity', $precision = 10, $scale = 3);
            $table->date('expiration_date');
            $table->date('arrival_date');
            $table->set('safety_classes', array_column(SafetyClass::cases(), 'value'));
            $table->set('ghs_symbols', array_column(GHSSymbol::cases(), 'value'));
            $table->string('unit')->default(Unit::KILOGRAM->value);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chemicals');
    }
};
