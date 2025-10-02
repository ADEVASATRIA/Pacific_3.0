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
    
    public function customer() {
        return $this->belongsTo(Customer::class);
    }
    
    public function purchaseDetails()
    {
        return $this->hasMany(PurchaseDetail::class, 'purchase_id');
    }

    
    public function staff() {
        return $this->belongsTo(User::class);
    }
    public function promo() {
		return $this->belongsTo(Promo::class);
	}
}
