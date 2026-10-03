<?php

namespace App\Models;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Database\Eloquent\BroadcastsEvents;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Equipment extends Model
{
    use HasFactory, BroadcastsEvents;

    protected $primaryKey = 'equipment_id';

    protected $fillable = [
        'location_id',
        'created_by',
        'equipment_name',
        'model',
        'serial_id',
        'status',
        'purchase_date',
        'warranty_expiration',
        'last_maintenance',
        'next_maintenance'
    ];

    protected function casts(): array
    {
        return [
            'purchase_date' => 'date',
            'warranty_expiration'    => 'date',
            'last_maintenance'    => 'date',
            'next_maintenance'    => 'date'
        ];
    }

    public function broadcastOn(string $event): array
    {
        return [new PrivateChannel('equipment')];
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
