<?php

namespace App\Models;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Database\Eloquent\BroadcastsEvents;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Location extends Model
{
    use HasFactory, BroadcastsEvents;

    protected $primaryKey = 'location_id';

    protected $fillable = [
        'created_by',
        'location_name',
        'description'
    ];

    public function broadcastOn(string $event): array
    {
        return [new PrivateChannel('locations')];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'user_id');
    }
}
