<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contract extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'type',
        'client_id',
        'number',
        'date',
        'date_start',
        'date_end',
        'comment',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
