<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alerts', function (Blueprint $table) {
            $table->id('alert_id');
            $table->foreignId('item_id');
            $table->foreignId('chemical_container_id')->nullable()->constrained('chemical_containers', 'chemical_container_id')->nullOnDelete();
            $table->text('message')->nullable();
            $table->string('alert_type')->nullable();
            $table->morphs('notifiable');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            $table->index(['chemical_container_id', 'alert_type', 'read_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alerts');
    }
};
