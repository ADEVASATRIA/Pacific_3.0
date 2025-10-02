<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketEntryLog extends Model
{
    protected $fillable = [
        'ticket_entry_id',
        'action',
        'created_at',
    ];
}
