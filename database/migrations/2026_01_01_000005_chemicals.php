<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\SafetyClass;
use App\Enums\GHSSymbol;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chemicals', function (Blueprint $table) {
            $table->id('chemical_id');
            $table->foreignId('created_by')->constrained('users', 'user_id');
            $table->string('chemical_name')->unique();
            $table->set('safety_classes', array_column(SafetyClass::cases(), 'value'));
            $table->set('ghs_symbols', array_column(GHSSymbol::cases(), 'value'));
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chemicals');
    }
};
