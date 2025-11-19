<?php

namespace App\Models;

use App\Enums\GasEventType;
use Illuminate\Database\Eloquent\Model;

class GasTransaction extends Model
{
    public $keyType = 'string';
    public $incrementing = false;
    protected $fillable = [
        'document_number',
        'event_type',
        'company_id',
        'notes',
        'created_by'
    ];

    protected $casts = [
        'event_type' => GasEventType::class
    ];
}
