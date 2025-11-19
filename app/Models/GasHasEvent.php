<?php

namespace App\Models;

use App\Enums\GasCylinderStatus;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class GasHasEvent extends Model
{
    use HasUlids;

    public $incrementing = false;
    public $keyType = 'string';

    protected $fillable = [
        'gas_cylinder_id',
        'transaction_id', // null for using, internal movement
        'company_id',
        'event_type', // Gas Event Type enums
        'location_from_id',
        'location_to_id',
        'cylinder_status_before',
        'cylinder_status_after',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'cylinder_status_before' => GasCylinderStatus::class,
        'cylinder_status_after' => GasCylinderStatus::class
    ];
}
