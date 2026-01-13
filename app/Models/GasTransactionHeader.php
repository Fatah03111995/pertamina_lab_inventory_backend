<?php

namespace App\Models;

use App\Enums\GasTransactionType;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class GasTransactionHeader extends Model
{
    use HasUlids;
    use SoftDeletes;

    public $keyType = 'string';
    public $incrementing = false;
    protected $fillable = [
        'transaction_type',
        'document_number',
        'evidence_document',
        'from_location_id',
        'to_location_id',
        'notes',
        'created_by'
    ];

    protected $casts = [
        'transaction_type' => GasTransactionType::class
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
     * Get the fromLocation that owns the GasTransactionHeader
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function fromLocation(): BelongsTo
    {
        return $this->belongsTo(GasLocation::class, 'from_location_id');
    }

    /**
     * Get the toLocation that owns the GasTransaction
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function toLocation(): BelongsTo
    {
        return $this->belongsTo(GasLocation::class, 'to_location_id');
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
        return $this->hasMany(GasTransaction::class, 'header_id');
    }
}
