<?php

namespace App\Models;

use App\Enums\GasCylinderStatus;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class GasCylinder extends Model
{
    use HasUlids;

    public $keyType = 'string';
    public $incrementing = false;
    protected $fillable = [
        'gas_type_id',
        'serial_number',
        'vendor_code',
        'current_location_id',
        'status',
        'company_owner_id'
    ];

    protected $casts = [
        'status' => GasCylinderStatus::class
    ];
}
