<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FuelRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'folio',
        'vehicle_id',
        'driver_id',
        'project_id',
        'date',
        'return_date',
        'shift', // D/N (texto libre)
        'np_text', // N/P (texto libre)
        'provider_client',
        'description',
        'destination',
        'initial_mileage',
        'final_mileage',
        'liters', // Consumo
        'fuel_price',
        'cost',
        'km_per_liter',
        'gasoline_cost', // $ Gasolina
        'diesel_cost', // $ Diesel
        'amount',
        'evidence',
    ];

    protected $casts = [
        'date' => 'date',
        'return_date' => 'date',
        'initial_mileage' => 'integer',
        'final_mileage' => 'integer',
        'liters' => 'decimal:2',
        'fuel_price' => 'decimal:2',
        'cost' => 'decimal:2',
        'km_per_liter' => 'decimal:2',
        'gasoline_cost' => 'decimal:2',
        'diesel_cost' => 'decimal:2',
        'amount' => 'decimal:2',
        'evidence' => 'array',
    ];

    /**
     * Get the vehicle that owns the fuel record.
     */
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * Get the driver that owns the fuel record.
     */
    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    /**
     * Get the project that owns the fuel record.
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the mileage traveled attribute.
     */
    public function getMileageTraveledAttribute()
    {
        return $this->final_mileage - $this->initial_mileage;
    }

    /**
     * Calculate and set km per liter before saving.
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($record) {
            if ($record->liters > 0 && $record->final_mileage > $record->initial_mileage) {
                $mileageTraveled = $record->final_mileage - $record->initial_mileage;
                $record->km_per_liter = round($mileageTraveled / $record->liters, 2);
            }
        });
    }

    /**
     * Scope a query to filter by date range.
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    /**
     * Scope a query to filter by current month.
     */
    public function scopeCurrentMonth($query)
    {
        return $query->whereMonth('date', now()->month)
                     ->whereYear('date', now()->year);
    }
}
