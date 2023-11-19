<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubService extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'service_id'];

    public function service_type()
    {
        return $this->belongsTo(ServiceType::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
