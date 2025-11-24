<?php

namespace App\Models;

use App\Enums\GasCompanyCategory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GasCompany extends Model
{
    use HasUlids;

    public $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'name',
        'category',
        'address',
        'contact'
    ];

    protected $casts = [
        'category' => GasCompanyCategory::class,
    ];

    /**
     * Relation hasMany
     */

    /**
     * Get all of the gasCylinders for the GasCompany
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function gasCylinders(): HasMany
    {
        return $this->hasMany(GasCylinder::class, 'company_owner_id');
    }
}
