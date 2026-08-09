<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chemicals', function (Blueprint $table) {
            $table->id('chemical_id');
            $table->foreignId('location_id')->constrained('locations')->references('location_id');
            $table->string('chemical_name');
            $table->enum(
                'hazard_color',
                [
                    'red',
                    'yellow',
                    'white',
                    'blue'
                ]
            )->default('red');
            $table->set(
                'ghs_symbols',
                [
                    'GHS01',
                    'GHS02',
                    'GHS03',
                    'GHS04',
                    'GHS05',
                    'GHS06',
                    'GHS07',
                    'GHS08',
                    'GHS09'
                ]
            )->default('GHS01');
            $table->enum(
                'unit',
                [
                    'kilogram',
                    'gram',
                    'milligram',
                    'microgram',
                    'liter',
                    'milliliter',
                    'microliter'
                ]
            )->default('kilogram');
            $table->decimal('quantity', $precision = 10, $scale = 3);
            $table->boolean('opened');
            $table->date('expiration_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chemicals');
    }
};
