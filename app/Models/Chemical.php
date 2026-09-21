<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    protected function casts(): array
    {
        return [
            'expiration_date' => 'date',
            'arrival_date'    => 'date',
        ];
    }

    public function alerts(): HasMany
    {
        return $this->hasMany(Alert::class, 'chemical_id', 'chemical_id');
    }
}
