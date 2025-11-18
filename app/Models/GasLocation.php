<?php

namespace App\Models;

use App\Enums\GasLocationCategory;
use App\Enums\GasLocationType;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use LogicException;

class GasLocation extends Model
{
    use HasUlids;

    public $keyType = 'string';
    public $incrementing = false;
    protected $fillable = [
        'name',
        'type',
        'category',
        'address'
    ];

    protected $casts = [
        'type' => GasLocationType::class,
        'category' => GasLocationCategory::class
    ];

    public function isExternal()
    {
        if (!$this->category) {
            throw new LogicException('GasLocation.category tidak boleh null');
        }
        return $this->category->isExternal();
    }

    public function isInternal()
    {
        if (!$this->category) {
            throw new LogicException('GasLocation.category tidak boleh null');
        }
        return $this->category->isInternal();
    }

    public function scopeInternal($q){
        return $q->where('category', GasLocationCategory::INTERNAL->value);
    }

    public function scopeExternal($q){
        return $q->where('category', GasLocationCategory::EXTERNAL->value);
    }
}
