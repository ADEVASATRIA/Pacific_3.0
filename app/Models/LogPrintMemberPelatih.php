<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogPrintMemberPelatih extends Model
{
    protected $fillable = [
        'customer_id',
        'ticket_id',
        'ticket_code',
        'customer_name',
        'phone',
        'status',
        'type',

    ];

    public function customer() {
        return $this->belongsTo(Customer::class);
    }

    public function ticket() {
        return $this->belongsTo(Ticket::class);
    }
}
