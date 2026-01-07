<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property Driver|null $driver
 * @property User|null $user
 */
class Trip extends Model
{
    protected $guarded = [];

    protected $casts = [
        'origin' => 'array',
        'destination' => 'array',
        'driver_location' => 'array',
        'is_started' => 'boolean',
        'is_complete' => 'boolean'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }
}
