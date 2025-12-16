<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CashInOut extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'cash_in_out';

    protected $fillable = [
        'cash_session_id',
        'nominal_uang',
        'type',
        'keterangan'
    ];

    public function cashSession(){
        return $this->belongsTo(CashSession::class);
    }
}
