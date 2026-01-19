<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'unit',
        'brand',
        'model',
        'year',
        'plate',
        'fuel_type_id',
        'tank_capacity',
        'initial_mileage',
        'color',
        'notes',
        'active',
    ];

    protected $casts = [
        'year' => 'integer',
        'tank_capacity' => 'decimal:2',
        'initial_mileage' => 'integer',
        'active' => 'boolean',
    ];

    /**
     * Get the fuel type that owns the vehicle.
     */
    public function fuelType()
    {
        return $this->belongsTo(FuelType::class);
    }

    /**
     * Get the fuel records for the vehicle.
     */
    public function fuelRecords()
    {
        return $this->hasMany(FuelRecord::class);
    }

    /**
     * Get the performances for the vehicle.
     */
    public function performances()
    {
        return $this->hasMany(VehiclePerformance::class);
    }

    /**
     * Get the current performance for the vehicle.
     */
    public function currentPerformance()
    {
        return $this->hasOne(VehiclePerformance::class)->where('current', true);
    }

    /**
     * Scope a query to only include active vehicles.
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    /**
     * Get the average fuel consumption.
     */
    public function getAverageConsumptionAttribute()
    {
        return $this->fuelRecords()->avg('km_per_liter');
    }

    /**
     * Get the total cost.
     */
    public function getTotalCostAttribute()
    {
        return $this->fuelRecords()->sum('cost');
    }

    /**
     * Get the total mileage traveled.
     */
    public function getTotalMileageAttribute()
    {
        return $this->fuelRecords()->sum('mileage_traveled');
    }
}
