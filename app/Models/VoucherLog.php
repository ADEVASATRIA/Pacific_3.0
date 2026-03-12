<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VoucherLog extends Model
{
    protected $fillable = [
        'id',
        'voucher_id',
        'customer_id',
        'code',
        'start_at',
        'end_at',
        'is_active',
    ];

    protected $table = 'voucher_log';
    
    public function voucher()
    {
        return $this->belongsTo(Voucher::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

}
