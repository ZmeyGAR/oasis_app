<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'full_address',
        'country',
        'region',
        'district',
        'locality',
        'street',
        'house_number',
        'house_frontway',
        'house_floor',
        'apartment',
        'intercom_code',
        'latitude',
        'longitude',
        'type',
    ];
}
