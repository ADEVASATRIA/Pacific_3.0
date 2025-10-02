<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PackageComboDetail extends Model
{
    protected $fillable = [
        'package_combo_id',
        'type',
        'item_id',
        'qty',
        'qty_extra'
    ];
    
    public function packageCombo() {
        return $this->belongsTo(PackageCombo::class);
    }
    
	public function ticketType() {
        return $this->belongsTo(TicketType::class, 'item_id');
    }
    
	// public function item() {
 //        return $this->belongsTo(Item::class, 'item_id');
 //    }
}
