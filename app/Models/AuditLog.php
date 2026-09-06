<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory;

    public const UPDATED_AT = null;

    protected $primaryKey = 'audit_log_id';

    protected $fillable = [
        'created_by',
        'audit_action',
        'target',
        'metadata'
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
