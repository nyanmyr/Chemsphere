<?php

namespace App\Models;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Database\Eloquent\BroadcastsEvents;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    use HasFactory, BroadcastsEvents;

    public const UPDATED_AT = null;

    protected $primaryKey = 'audit_log_id';

    protected $fillable = [
        'created_by',
        'audit_action',
        'target',
        'metadata'
    ];

    protected static function booted(): void
    {
        static::updating(function () {
            throw new \RuntimeException('Error: Audit logs cannot be updated.');
        });

        static::deleting(function () {
            throw new \RuntimeException('Error: Audit logs cannot be deleted.');
        });
    }

    public function broadcastOn(string $event): array
    {
        return [new PrivateChannel('audit_logs')];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'user_id');
    }
}
