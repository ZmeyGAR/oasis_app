<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'type',
        'city_id',
        'address',
        'RNN',
        'IIK',
        'BIN',
        'BIK',
        'BANK',
        'KBE',
    ];

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }
}
