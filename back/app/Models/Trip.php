<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trip extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'origin' => 'array',
        'destination' => 'array',
        'driver_location' => 'array',
        'is_started' => 'boolean',
        'is_completed' => 'boolean'
    ];

    public function user()
    {
        return $this->belongsTo(related: User::class);
    }

    public function driver()
    {
        return $this->belongsTo(related: Driver::class);
    }
}
