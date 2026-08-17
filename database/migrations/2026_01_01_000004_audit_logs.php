<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->string('audit_log_id')->primary();
            $table->foreignId('user_id')->constrained('users')->references('user_id');
            $table->enum(
                'audit_action_type',
                [
                    'INSERT',
                    'UPDATE',
                    'DELETE'
                ]
            )->default('INSERT');
            $table->text('target');
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
