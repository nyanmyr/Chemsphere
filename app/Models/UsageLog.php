<?php

namespace App\Models;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Database\Eloquent\BroadcastsEvents;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UsageLog extends Model
{
    use HasFactory, BroadcastsEvents;

    public const UPDATED_AT = null;

    protected $primaryKey = 'usage_log_id';

    protected $fillable = [
        'created_by',
        'location_id',
        'item_type',
        'item_id',
        'quantity_used',
        'quantity_remaining',
        'notes'
    ];

    protected static function booted(): void
    {
        static::updating(function () {
            throw new \RuntimeException('Error: Usage logs cannot be updated.');
        });

        static::deleting(function () {
            throw new \RuntimeException('Error: Usage logs cannot be deleted.');
        });
    }

    public function broadcastOn(string $event): array
    {
        return [new PrivateChannel('usage_logs')];
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_id', 'location_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'user_id');
    }
}
