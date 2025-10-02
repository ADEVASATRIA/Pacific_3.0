<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketEntry extends Model
{
    protected $table = 'ticket_entries'; // atau sesuai nama tabelmu
    public $timestamps = false;
    protected $fillable = [
        'ticket_id',
        'date_valid',
        'code',
        'status',
        'type',
        'created_at',
    ];
    
    public function ticket() {
        return $this->belongsTo(Ticket::class);
    }
    
    public function logs() {
        return $this->hasMany(TicketEntryLog::class);
    }
}
