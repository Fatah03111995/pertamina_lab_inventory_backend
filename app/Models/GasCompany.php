<?php

namespace App\Models;

use App\Enums\GasCompanyCategory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

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
}
