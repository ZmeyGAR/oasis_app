<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShipingAddressWorkTime extends Model
{
    use HasFactory;

    protected $fillable = [
        'start_at',
        'end_at',
        'launch_start_at',
        'launch_end_at',
        'weekend_days',
    ];

    protected $casts = [
        'weekend_days' => 'array'
    ];
}
