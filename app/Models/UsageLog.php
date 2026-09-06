<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsageLog extends Model
{
    use HasFactory;

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
            throw new \RuntimeException('Error: Audit logs cannot be updated.');
        });

        static::deleting(function () {
            throw new \RuntimeException('Error: Audit logs cannot be deleted.');
        });
    }
}
