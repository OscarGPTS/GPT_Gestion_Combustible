<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FuelType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'display_name',
    ];

    /**
     * Get the vehicles for the fuel type.
     */
    public function vehicles()
    {
        return $this->hasMany(Vehicle::class);
    }
}
