<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Customer extends Model
{
    use HasFactory;

    protected $table = 'customers';

    protected $fillable = [
        'name', 'email', 'phone', 'person_type'
    ];

    public function details(): HasMany
    {
        return $this->hasMany(CustomerDetail::class);
    }

    public function payments(): BelongsToMany
    {
        return $this->belongsToMany(Payment::class);
    }

    public function shipings(): HasMany
    {
        return $this->hasMany(CustomerShiping::class);
    }

    public function individual_price(): HasMany
    {
        return $this->hasMany(CustomerIndividualPrice::class);
    }

    public function talons(): HasMany
    {
        return $this->hasMany(CustomerTalonBalance::class);
    }
}
