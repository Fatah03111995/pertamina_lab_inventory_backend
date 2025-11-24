<?php

namespace App\Models;

use App\Enums\GasLocationCategory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GasLocation extends Model
{
    use HasUlids;

    public $keyType = 'string';
    public $incrementing = false;
    protected $fillable = [
        'name',
        'code', // unique
        'category',
        'address'
    ];

    protected $casts = [
        'category' => GasLocationCategory::class
    ];

    /**
     * HELPER
     */

    public function isStorage(): bool
    {
        return $this->category === GasLocationCategory::STORAGE;
    }

    public function isMaintenance(): bool
    {
        return $this->category === GasLocationCategory::MAINTENANCE;
    }

    public function isVendor(): bool
    {
        return $this->category === GasLocationCategory::VENDOR;
    }

    public function isConsumption(): bool
    {
        return $this->cateogry === GasLocationCategory::CONSUMPTION;
    }

    /**
     * Relation
     */

    /**
     * Get all of the gasCylinders for the GasLocation
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function gasCylinders(): HasMany
    {
        return $this->hasMany(GasCylinder::class, 'current_location_id');
    }
}
