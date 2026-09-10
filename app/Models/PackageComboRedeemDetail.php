<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PackageComboRedeemDetail extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'package_combo_redeem_id',
        'package_combo_detail_id',
        'name',
        'qty',
        'qty_extra',
        'qty_redeemed',
        'qty_printed'
    ];
    
    public function packageComboRedeem() {
        return $this->belongsTo(PackageComboRedeem::class);
    }
    
    public function packageComboDetail() {
        return $this->belongsTo(PackageComboDetail::class);
    }
        
    public function tickets() {
        return $this->hasMany(Ticket::class);
    }
}
