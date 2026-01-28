<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Unit extends Model
{
    protected $fillable = [
        'name',
        'type',
        'conversion_factor',
    ];

    public function measurementType(): BelongsTo
    {
        return $this->belongsTo(MeasurementType::class);
    }
}
