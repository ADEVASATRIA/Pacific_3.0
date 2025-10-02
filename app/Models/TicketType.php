<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketType extends Model
{
    protected $fillable = [
        'name',
        'price',
        'duration',
        'qty_extra',
        'weight',
        'is_dob_mandatory',
        'is_phone_mandatory',
        'is_active',
        'can_buy_ticket_pengantar',
        'tipe_khusus',
        'ticket_kode_ref'
    ];
    
    public function ticket(){
        return $this->hasMany(Ticket::class);
    }
    
    public function clubhouse(){
        return $this->belongsTo(Clubhouse::class);
    }
    
    public function purchaseDetail(){
        return $this->hasMany(PurchaseDetail::class);
    }
}
