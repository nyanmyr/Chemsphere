<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsageLog extends Model
{
    use HasFactory;

    protected $primaryKey = 'usage_log_id';

    protected $fillable = [
        'user_id',
        'location_id',
        'item_type',
        'item_id',
        'quantity_used',
        'quantity_remaining',
        'notes'
    ];
}
