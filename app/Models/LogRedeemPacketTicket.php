<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogRedeemPacketTicket extends Model
{
    protected $fillable = [
        'customer_id',
        'customer_name',
        'phone',
        'redeem_qty',
        'status',
    ];

    public function customer() {
        return $this->belongsTo(Customer::class);
    }
}
