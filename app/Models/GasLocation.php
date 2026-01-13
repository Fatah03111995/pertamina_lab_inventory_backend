<?php

namespace App\Models;

use App\Enums\GasLocationCategory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class GasLocation extends Model
{
    use HasUlids;
    use SoftDeletes;

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
        return $this->category->isStorage();
    }

    public function isMaintenance(): bool
    {
        return $this->category->isMaintenance();
    }

    public function isRefilling(): bool
    {
        return $this->category->isRefilling();
    }

    public function isConsumption(): bool
    {
        return $this->category->isConsumption();
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
