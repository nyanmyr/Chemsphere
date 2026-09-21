<?php

namespace App\Models;

use App\AlertType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Alert extends Model
{
    use HasFactory;

    protected $primaryKey = 'alert_id';

    protected $fillable = [
        'chemical_id',
        'message',
        'alert_type',
        'notifiable_id',
        'notifiable_type',
        'read_at',
    ];

    protected function casts(): array
    {
        return [
            'alert_type' => AlertType::class,
            'read_at' => 'datetime',
        ];
    }

    public function chemical(): BelongsTo
    {
        return $this->belongsTo(Chemical::class, 'chemical_id', 'chemical_id');
    }

    public function notifiable(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    public function markAsRead(): void
    {
        if (is_null($this->read_at)) {
            $this->forceFill(['read_at' => now()])->save();
        }
    }
}
