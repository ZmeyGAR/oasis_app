<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerIndividualPrice extends Model
{
    use HasFactory;

    protected $fillable = ['price', 'product_id'];
}
