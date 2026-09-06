<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory;

    protected $primaryKey = 'audit_log_id';

    protected $fillable = [
        'created_by',
        'audit_action',
        'target',
        'metadata'
    ];
}
