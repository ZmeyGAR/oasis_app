<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubContract extends Model
{
    use HasFactory;

    protected $fillable = [
        'number',
        'contract_id',
        'date_start',
        'date_end',
    ];

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }
}
