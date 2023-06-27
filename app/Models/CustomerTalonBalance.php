<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerTalonBalance extends Model
{
    use HasFactory;

    protected $fillable = [
        'balance',
        'description',
    ];
}
