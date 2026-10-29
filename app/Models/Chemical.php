<?php

namespace App\Models;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Database\Eloquent\BroadcastsEvents;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Chemical extends Model
{
    use HasFactory, BroadcastsEvents;

    protected $primaryKey = 'chemical_id';

    protected $fillable = [
        'created_by',
        'chemical_name',
        'safety_classes',
        'ghs_symbols',
    ];

    public function batches(): HasMany
    {
        return $this->hasMany(ChemicalBatch::class, 'chemical_id', 'chemical_id');
    }

    public function broadcastOn(string $event): array
    {
        return [new PrivateChannel('inventory')];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'user_id');
    }
}
