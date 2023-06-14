<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

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
    ];

    public function customer(): HasOne
    {
        return $this->hasOne(Customer::class);
    }
}
