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
        'actual_city_id',
        'legal_city_id',
        'address',
        'actual_address',
        'legal_address',
        'RNN',
        'IIK',
        'BIN',
        'BIK',
        'BANK',
        'KBE',
        'manager',
        'contacts',
    ];

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function actual_city()
    {
        return $this->belongsTo(City::class, 'actual_city_id');
    }
    public function legal_city()
    {
        return $this->belongsTo(City::class, 'legal_city_id');
    }
}
