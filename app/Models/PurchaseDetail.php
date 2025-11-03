<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseDetail extends Model
{
    public const TYPE_TICKET = 1;
	public const TYPE_ITEM = 2;
    public const TYPE_PACKAGE_COMBO = 3;
    
    protected $fillable = [
        'purchase_id',
        'type',
        'purchase_item_id',
        'name',
        'qty',
        'qty_extra',
        'price',
        'ticket_kode_ref',
    ];
    
    public function purchase()
    {
        return $this->belongsTo(Purchase::class, 'purchase_id');
    }
        
    public function ticketType()
    {
        return $this->belongsTo(TicketType::class, 'purchase_item_id');
    }

    
	// public function item() {
 //        return $this->belongsTo(Item::class, 'purchase_item_id');
 //    }
    
    public function packageCombo() {
        return $this->belongsTo(PackageCombo::class, 'purchase_item_id');
    }
    
    public function ticket() {
        return $this->hasOne(Ticket::class);
    }
    public function tickets() {
        return $this->hasMany(Ticket::class, 'purchase_detail_id');
    }
    
    public function packageComboRedeem()
    {
        return $this->hasOne(PackageComboRedeem::class, 'purchase_detail_id');
    }
        
    public function redeems()
    {
        return $this->hasManyThrough(
            PackageComboRedeemDetail::class,
            PackageComboRedeem::class,
            'package_combo_id', 
            'package_combo_redeem_id', 
            'purchase_item_id',
            'id' 
        );
    }
    
    public function packageComboRedeemDetails()
    {
        return $this->hasManyThrough(
            PackageComboRedeemDetail::class,
            PackageComboRedeem::class,
            'package_combo_id', 
            'package_combo_redeem_id', 
            'purchase_item_id', 
            'id'
        );
    }
}
