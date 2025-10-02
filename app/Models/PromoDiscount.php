<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromoDiscount extends Model
{
    protected $fillable = [
        'item_id',
        'discount_price',
        'start_date',
        'expired_date',
    ];
}
