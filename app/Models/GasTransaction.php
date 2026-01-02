<?php

namespace App\Models;

use App\Enums\GasEventType;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class GasTransaction extends Model
{
    use HasUlids;
    use SoftDeletes;

    public $keyType = 'string';
    public $incrementing = false;
    protected $fillable = [
        'event_type',
        'document_number',
        'evidence_document',
        'to_location_id',
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
     * Get the toLocation that owns the GasTransaction
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function toLocation(): BelongsTo
    {
        return $this->belongsTo(GasLocation::class, 'gas_location_id');
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
        return $this->hasMany(GasEvent::class, 'gas_transaction_id');
    }
}
