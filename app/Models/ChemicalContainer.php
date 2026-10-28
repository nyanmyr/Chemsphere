<?php

namespace App\Models;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Database\Eloquent\BroadcastsEvents;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChemicalContainer extends Model
{
    use HasFactory, BroadcastsEvents;

    protected $table = 'chemical_containers';
    protected $primaryKey = 'chemical_container_id';

    protected $fillable = [
        'chemical_id', 'location_id', 'created_by', 'batch_number', 'brand_name',
        'quantity_received', 'current_quantity', 'expiration_date', 'date_received',
    ];

    protected function casts(): array
    {
        return ['expiration_date' => 'date', 'date_received' => 'date'];
    }

    public function chemical(): BelongsTo
    {
        return $this->belongsTo(Chemical::class, 'chemical_id', 'chemical_id');
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_id', 'location_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'user_id');
    }

    public function usageLogs(): HasMany
    {
        return $this->hasMany(UsageLog::class, 'chemical_container_id', 'chemical_container_id');
    }

    public function alerts(): HasMany
    {
        return $this->hasMany(Alert::class, 'chemical_container_id', 'chemical_container_id');
    }

    public function broadcastOn(string $event): array
    {
        return [new PrivateChannel('inventory')];
    }

    public function getChemicalNameAttribute(): ?string { return $this->chemical?->chemical_name; }
    public function getSafetyClassesAttribute(): string { return (string) ($this->chemical?->safety_classes ?? ''); }
    public function getGhsSymbolsAttribute(): string { return (string) ($this->chemical?->ghs_symbols ?? ''); }
    public function getQuantityPerUnitAttribute(): float { return (float) ($this->chemical?->quantity_per_unit ?? 0); }
    public function getUnitAttribute(): ?string { return $this->chemical?->unit; }

    public function quantityType(): string
    {
        return $this->chemical?->quantityType() ?? 'quantity';
    }
}
