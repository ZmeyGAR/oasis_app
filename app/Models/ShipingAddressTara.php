<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShipingAddressTara extends Model
{
    use HasFactory;

    protected $fillable = [
        // 'having', 
        'count'
    ];

    protected $casts = [
        // 'having'    => 'boolean',
        'count'     => 'integer',
    ];
}
