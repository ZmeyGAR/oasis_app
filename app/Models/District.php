<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class District extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'state_id', 'area_id'
    ];

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }
}
