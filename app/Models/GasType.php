<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GasType extends Model
{
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
}
