<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{
    use HasFactory;

    protected $primaryKey = 'equipment_id';

    protected $fillable = [
        'location_id',
        'created_by',
        'equipment_name',
        'model',
        'serial_id',
        'status',
        'quantity',
        'purchase_date',
        'warranty_expiration',
        'last_maintenance',
        'next_maintenance'
    ];
}
