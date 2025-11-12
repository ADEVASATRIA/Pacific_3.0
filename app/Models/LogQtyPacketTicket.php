<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogQtyPacketTicket extends Model
{
    protected $fillable = [
        'log_redeem_packet_tickets_id ',
        'package_combo_redeem_id',
        'package_combo_detail_id',      
    ];

    public function package_combo_redeem() {
        return $this->belongsTo(PackageComboRedeem::class);
    }

    public function package_combo_detail() {
        return $this->belongsTo(PackageComboDetail::class);
    }

    public function log_redeem_packet_tickets() {
        return $this->belongsTo(LogRedeemPacketTicket::class);
    }

    public function ticket() {
        return $this->belongsTo(Ticket::class);
    }
}
