<?php

namespace App\Models;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Database\Eloquent\BroadcastsEvents;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
