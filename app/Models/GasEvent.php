<?php

namespace App\Models;

use App\Enums\GasCylinderStatus;
use App\Enums\GasEventType;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class GasEvent extends Model
{
    use HasUlids;
    use SoftDeletes;

    public $incrementing = false;
    public $keyType = 'string';

    protected $fillable = [
        'gas_cylinder_id',
        'gas_transaction_id', // null for using, internal movement
        'event_type', // Gas Event Type enums
        'from_location_id',
        'to_location_id',
        'from_status',
        'to_status',
        'notes',
        'created_by',
        'metadata'
    ];

    protected $casts = [
        'event_type' => GasEventType::class,
        'from_status' => GasCylinderStatus::class,
        'to_status' => GasCylinderStatus::class,
        'metadata' => 'array'
    ];

    /**
     * Relation belongTo
     */

    /**
     * Get the gasCylinder that owns the GasHasEvent
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function gasCylinder(): BelongsTo
    {
        return $this->belongsTo(GasCylinder::class, 'gas_cylinder_id');
    }

    /**
     * Get the gasTransaction that owns the GasHasEvent
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function gasTransaction(): BelongsTo
    {
        return $this->belongsTo(GasTransaction::class, 'gas_transaction_id');
    }

    /**
     * Get the locationFrom that owns the GasHasEvent
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function fromLocation(): BelongsTo
    {
        return $this->belongsTo(GasLocation::class, 'from_location_id');
    }

    /**
     * Get the locationTo that owns the GasHasEvent
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function toLocation(): BelongsTo
    {
        return $this->belongsTo(GasLocation::class, 'to_location_id');
    }

    /**
     * Get the creator that owns the GasHasEvent
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
