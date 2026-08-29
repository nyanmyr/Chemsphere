<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chemical extends Model
{
    use HasFactory;

    protected $primaryKey = 'chemical_id';

    protected $fillable = [
        'location_id',
        'created_by',
        'chemical_name',
        'batch_number',
        'brand_name',
        'volume_per_unit',
        'initial_quantity',
        'current_quantity',
        'expiration_date',
        'arrival_date',
        'safety_classes',
        'ghs_symbols',
        'unit'
    ];
}
