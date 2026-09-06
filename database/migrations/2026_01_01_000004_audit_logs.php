<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id('audit_log_id');
            $table->foreignId('created_by')->constrained('users')->references('user_id');
            $table->string('audit_action');
            $table->text('target');
            $table->json('metadata')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        DB::unprepared("
            CREATE TRIGGER prevent_audit_logs_update
            BEFORE UPDATE on audit_logs
            FOR EACH ROW
            BEGIN
                SIGNAL SQLSTATE '45000'
                SET MESSAGE_TEXT = 'Error: this table is immutable. Updates are forbidden.';
            END
        ");

        DB::unprepared("
            CREATE TRIGGER prevent_audit_logs_delete
            BEFORE UPDATE on audit_logs
            FOR EACH ROW
            BEGIN
                SIGNAL SQLSTATE '45000'
                SET MESSAGE_TEXT = 'Error: this table is immutable. Deletions are forbidden.';
            END
        ");
    }

    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS prevent_audit_logs_update');
        DB::unprepared('DROP TRIGGER IF EXISTS prevent_audit_logs_delete');
        Schema::dropIfExists('audit_logs');
    }
};
