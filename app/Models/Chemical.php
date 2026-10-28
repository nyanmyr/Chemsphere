<?php

namespace App\Models;

use App\Enums\Unit;
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

    protected $fillable = ['chemical_name', 'quantity_per_unit', 'safety_classes', 'ghs_symbols', 'unit', 'created_by'];

    public function containers(): HasMany
    {
        return $this->hasMany(ChemicalContainer::class, 'chemical_id', 'chemical_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'user_id');
    }

    public function quantityType(): string
    {
        return Unit::tryFrom((string) $this->unit)?->quantityType() ?? 'quantity';
    }

    public function getTotalCurrentQuantityAttribute(): int
    {
        return (int) $this->containers()->sum('current_quantity');
    }

    public function broadcastOn(string $event): array
    {
        return [new PrivateChannel('inventory')];
    }
}
