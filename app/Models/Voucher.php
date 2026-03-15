<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Voucher extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'id',
        'name',
        'requirements',
        'type_voucher',
        'value',
        'quota',
        'start_date',
        'end_date',
        'min_purchase',
        'max_discount',
        'is_active',
    ];

    protected $table = 'vouchers';

    public function voucherLog(){
        return $this->hasMany(VoucherLog::class);
    }

    public function purchase(){
        return $this->hasMany(Purchase::class);
    }

    public function getBadgeHtml($value)
    {
        if ($value) {
            return '<span class="badge bg-success">YES</span>';
        }

        return '<span class="badge bg-danger">NO</span>';
    }

}
