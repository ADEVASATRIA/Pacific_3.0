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

    public function purchase()
    {
        return $this->hasOne(Purchase::class);
    }



    public function getBadgeHtml($value)
    {
        if ($value) {
            return '<span class="badge bg-success">YES</span>';
        }

        return '<span class="badge bg-danger">NO</span>';
    }

}
