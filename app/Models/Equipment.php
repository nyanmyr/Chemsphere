<?php

namespace App\Models;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Database\Eloquent\BroadcastsEvents;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'uic_asset_code',
        'src_asset_code',
        'status',
        'date_received',
        'preventive_maintenance_date',
        'next_due'
    ];

    protected function casts(): array
    {
        return [
            'date_received' => 'date',
            'preventive_maintenance_date' => 'date',
            'next_due' => 'date'
        ];
    }

    public function alerts(): HasMany
    {
        return $this->hasMany(Alert::class, 'equipment_id', 'equipment_id');
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
