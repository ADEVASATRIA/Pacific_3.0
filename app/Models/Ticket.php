<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = [
        'purchase_detail_id',
        'package_combo_redeem_detail_id',
        'customer_id',
        'code',
        'ticket_kode_ref',
        'date_start',
        'date_end',
        'is_active'
    ];

    public function isLifetime(): bool
    {
        return is_null($this->date_end);
    }

    public function scopeValid($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('date_end')
            ->orWhere('date_end', '>=', now());
        });
    }

    public function purchaseDetail()
    {
        return $this->belongsTo(PurchaseDetail::class);
    }
    
    public function packageComboRedeemDetail()
    {
        return $this->belongsTo(PackageComboRedeemDetail::class);
    }
    
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
    
    public function entries()
    {
        return $this->hasMany(TicketEntry::class);
    }
}
