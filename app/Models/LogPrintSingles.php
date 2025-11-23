<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogPrintSingles extends Model
{
    protected $fillable = [
        'customer_id',
        'ticket_id',
        'ticket_code',
        'customer_name',
        'phone',
        'status',
        'name_tickets'
    ];

    public function customer(){
        return $this->belongsTo(Customer::class,'customer_id');
    }

    public function  ticket(){
        return $this->belongsTo(Ticket::class,'ticket_id');
    }
}
