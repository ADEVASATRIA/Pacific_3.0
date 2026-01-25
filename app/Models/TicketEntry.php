<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Str;

class TicketEntry extends Model
{
    public const STATUS_NEW = 0;
    public const STATUS_INSIDE = 1;
    public const STATUS_OUTSIDE = 2;

    public const STATUSES = [
        self::STATUS_NEW => 'New',
        self::STATUS_INSIDE => 'Inside',
        self::STATUS_OUTSIDE => 'Outside'
    ];
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

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function logs()
    {
        return $this->hasMany(TicketEntryLog::class);
    }
    public static function generateCodeFast($ticketID)
	{
		return strtoupper('ENT' . $ticketID . '-' . Str::random(8));
	}
}
