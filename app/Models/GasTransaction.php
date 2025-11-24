<?php

namespace App\Models;

use App\Enums\GasEventType;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GasTransaction extends Model
{
    use HasUlids;

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

    /**
     * Relation belongTo
     */

    /**
     * Get the creator that owns the GasTransaction
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }


    /**
     * Relation Has Many
     */

    /**
     * Get all of the events for the GasTransaction
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function events(): HasMany
    {
        return $this->hasMany(GasHasEvent::class, 'gas_transaction_id');
    }
}
