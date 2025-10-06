<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    public const STATUS_NEW = 0;
    public const STATUS_PENDING = 1;
    public const STATUS_PAID = 2;

    protected $fillable = [
        'customer_id',
        'promo_id',
        'staff_id',
        'invoice_no',
        'sub_total',
        'tax',
        'total',
        'kembalian',
        'uangDiterima',
        'payment',
        'payment_info',
        'approval_code',
        'status'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function purchaseDetails()
    {
        return $this->hasMany(PurchaseDetail::class, 'purchase_id');
    }


    public function staff()
    {
        return $this->belongsTo(Admin::class);
    }
    public function promo()
    {
        return $this->belongsTo(Promo::class);
    }

    public function getPaymentLabelAttribute()
    {
        $labels = [
            1 => 'Cash',
            2 => 'QRIS BCA',
            3 => 'QRIS Mandiri',
            4 => 'Debit BCA',
            5 => 'Debit Mandiri',
            6 => 'Transfer',
            7 => 'QRIS BRI',
            8 => 'Debit BRI',
        ];

        return $labels[$this->payment] ?? 'Tidak Diketahui';
    }

}
