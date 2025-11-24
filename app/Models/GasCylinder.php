<?php

namespace App\Models;

use App\Enums\GasCylinderStatus;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GasCylinder extends Model
{
    use HasUlids;

    public $keyType = 'string';
    public $incrementing = false;
    protected $fillable = [
        'name',
        'gas_type_id',
        'serial_number',
        'vendor_code',
        'status',
        'current_location_id',
        'company_owner_id',
        'metadata'
    ];

    protected $casts = [
        'status' => GasCylinderStatus::class,
        'metadata' => 'array'
    ];

    /**
     * HELPER
     */

    public function isReadyToUse() : bool
    {
        return in_array($this->status, [
            GasCylinderStatus::FILLED
        ]);
    }

    /**
     * Relation BelongTo
     */

    /**
     * Get the gasType that owns the GasCylinder
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function gasType(): BelongsTo
    {
        return $this->belongsTo(GasType::class, 'gas_type_id');
    }

    /**
     * Get the currentLocation that owns the GasCylinder
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function currentLocation(): BelongsTo
    {
        return $this->belongsTo(GasLocation::class, 'current_location_id');
    }

    /**
     * Get the companyOwner that owns the GasCylinder
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function companyOwner(): BelongsTo
    {
        return $this->belongsTo(GasCompany::class, 'company_owner_id');
    }

    /**
     * Relation hasMany
     */

    /**
     * Get all of the events for the GasCylinder
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function events(): HasMany
    {
        return $this->hasMany(GasHasEvent::class, 'gas_cylinder_id');
    }
}
