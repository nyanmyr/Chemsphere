<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ghs_symbols', function (Blueprint $table) {
            $table->id('ghs_symbol_id');
            $table->string('ghs_symbol_name');
            $table->text('description')->nullable();
            $table->date('expiration_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ghs_symbols');
    }
};
