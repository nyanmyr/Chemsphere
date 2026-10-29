<?php

namespace App\Models;

use App\Enums\Unit;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Database\Eloquent\BroadcastsEvents;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChemicalBatch extends Model
{
    use HasFactory, BroadcastsEvents;

    protected $primaryKey = 'chemical_batch_id';

    protected $fillable = [
        'chemical_id',
        'location_id',
        'created_by',
        'batch_number',
        'brand_name',
        'quantity_per_unit',
        'quantity_received',
        'current_quantity',
        'expiration_date',
        'date_received',
        'unit',
    ];

    protected function casts(): array
    {
        return [
            'expiration_date' => 'date',
            'date_received' => 'date',
        ];
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

    public function alerts(): HasMany
    {
        return $this->hasMany(Alert::class, 'item_id', 'chemical_batch_id')
            ->whereIn('alert_type', ['expiring soon', 'expired', 'low stock', 'out of stock']);
    }

    public function usageLogs(): HasMany
    {
        return $this->hasMany(UsageLog::class, 'item_id', 'chemical_batch_id')
            ->where('item_type', 'chemical');
    }

    public function broadcastOn(string $event): array
    {
        return [new PrivateChannel('inventory')];
    }

    public function quantityType(): string
    {
        return Unit::tryFrom((string) $this->unit)?->quantityType() ?? 'quantity';
    }
}
