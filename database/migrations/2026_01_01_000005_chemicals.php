<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\SafetyClass;
use App\Enums\GHSSymbol;
use App\Enums\Unit;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chemicals', function (Blueprint $table) {
            $table->id('chemical_id');
            $table->string('chemical_name')->unique();
            $table->decimal('quantity_per_unit', 8, 2)->default(0);
            $table->string('safety_classes')->default('');
            $table->string('ghs_symbols')->default('');
            $table->string('unit')->default(Unit::KILOGRAM->value);
            $table->foreignId('created_by')->nullable()->constrained('users', 'user_id')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chemicals');
    }
};
