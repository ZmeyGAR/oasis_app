<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ContractServices extends Model
{

    protected $fillable = [
        'contract_id',
        'state_id',
        'count',
    ];

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(ServiceType::class);
    }
    public function programs(): BelongsToMany
    {
        return $this->belongsToMany(ProgramType::class);
    }
}
