<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PackageComboRedeem extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'purchase_detail_id',
        'package_combo_id',
        'customer_id',
        'name',
        'price',
        'expired_date',
        'fully_redeemed_at',
    ];
    
    public function purchaseDetail() {
        return $this->belongsTo(PurchaseDetail::class);
    }
    
    public function packageCombo()
    {
        return $this->belongsTo(PackageCombo::class);
    }
        
    
    public function customer() {
        return $this->belongsTo(Customer::class);
    }
    
    public function details() {
        return $this->hasMany(PackageComboRedeemDetail::class);
    }
}
