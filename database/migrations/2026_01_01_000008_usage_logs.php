<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\ItemType;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usage_logs', function (Blueprint $table) {
            $table->id('usage_log_id');
            $table->foreignId('created_by')->constrained('users')->references('user_id');
            $table->foreignId('location_id')->constrained('locations')->references('location_id');
            $table->string('item_type')->default(ItemType::CHEMICAL->value);
            $table->unsignedBigInteger('item_id');
            $table->decimal('quantity_used', $precision = 10, $scale = 3);
            $table->decimal('quantity_remaining', $precision = 10, $scale = 3);
            $table->text('notes')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        DB::unprepared("
            CREATE TRIGGER prevent_usage_logs_update
            BEFORE UPDATE on usage_logs
            FOR EACH ROW
            BEGIN
                SIGNAL SQLSTATE '45000'
                SET MESSAGE_TEXT = 'Error: this table is immutable. Updates are forbidden.';
            END
        ");

        DB::unprepared("
            CREATE TRIGGER prevent_usage_logs_delete
            BEFORE UPDATE on usage_logs
            FOR EACH ROW
            BEGIN
                SIGNAL SQLSTATE '45000'
                SET MESSAGE_TEXT = 'Error: this table is immutable. Deletions are forbidden.';
            END
        ");
    }

    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS prevent_usage_logs_update');
        DB::unprepared('DROP TRIGGER IF EXISTS prevent_usage_logs_delete');
        Schema::dropIfExists('usage_logs');
    }
};
