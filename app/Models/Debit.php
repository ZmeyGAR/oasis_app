<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Debit extends Model
{
    protected $fillable = [
        'period',
        'status',
    ];

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function contract_services()
    {
        return $this->belongsToMany(ContractServices::class)->withPivot('count', 'sum')->withTimestamps();
    }
}
