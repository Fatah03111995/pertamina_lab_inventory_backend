<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GasType extends Model
{
    protected $fillable = [
        'name',
        'min_stock',
        'safety_info',
        'description'
    ];

    protected $casts = [
        'min_stock' => 'integer'
    ];
}
