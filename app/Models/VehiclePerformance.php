<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehiclePerformance extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_id',
        'fuel_price',
        'performance',
        'effective_date',
        'current',
    ];

    protected $casts = [
        'fuel_price' => 'decimal:2',
        'performance' => 'decimal:2',
        'effective_date' => 'date',
        'current' => 'boolean',
    ];

    /**
     * Get the vehicle that owns the performance.
     */
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * Scope a query to only include current performances.
     */
    public function scopeCurrent($query)
    {
        return $query->where('current', true);
    }
}
