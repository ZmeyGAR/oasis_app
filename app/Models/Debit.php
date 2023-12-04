<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Debit extends Model
{
    protected $fillable = [

        'activity_type',
        'count',
        'period',
        'status',

        'contract_id',

        'indicator_id',
        'program_id',

        'state_id',
        'area_id',
        'district_id',
        'city_id',
        'station_id',
    ];

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(ServiceType::class);
    }

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }
    public function indicator()
    {
        return $this->belongsTo(Indicator::class);
    }
    public function state()
    {
        return $this->belongsTo(State::class);
    }
    public function area()
    {
        return $this->belongsTo(Area::class);
    }
    public function district()
    {
        return $this->belongsTo(District::class);
    }
    public function city()
    {
        return $this->belongsTo(City::class);
    }
    public function station()
    {
        return $this->belongsTo(Station::class);
    }
}
