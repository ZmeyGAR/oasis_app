<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerShiping extends Model
{
    use HasFactory;

    protected $fillable = [
        'isMain',
        'address_name',
        'firstname',
        'lastname',
        'email',
        'phone',
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

        'comment',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function deliveries(): HasOne
    {
        return $this->hasOne(ShipingDelivery::class);
    }

    public function work_time(): HasOne
    {
        return $this->hasOne(ShipingAddressWorkTime::class);
    }



    public function cooler(): HasOne
    {
        return $this->hasOne(ShipingAddressCooler::class);
    }

    public function tara(): HasOne
    {
        return $this->hasOne(ShipingAddressTara::class);
    }

    public function balance(): HasMany
    {
        return $this->hasMany(ShipingAddressBalance::class)->orderBy('created_at', 'desc');
    }
}
