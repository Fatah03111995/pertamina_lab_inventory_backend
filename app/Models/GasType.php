<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class GasType extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'name',
        'min_stock',
        'safety_info',
        'description'
    ];

    protected $casts = [
        'min_stock' => 'integer'
    ];

    /**
     * Get all of the gasCylinders for the GasType
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function gasCylinders(): HasMany
    {
        return $this->hasMany(GasCylinder::class, 'gas_type_id');
    }

    public function getTotalGasCylinders(): int
    {
        return $this->gasCylinders()->count();
    }
}
