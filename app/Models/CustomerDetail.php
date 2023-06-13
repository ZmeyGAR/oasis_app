<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerDetail extends Model
{
    use HasFactory;

    protected $table = 'customer_details';

    protected $fillable = [
        'name',
        'ownership',
        'BIN_IIN',
        'bank_account',
        'legal_address',
        'KBE',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
