<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashSession extends Model
{
    use HasFactory;
    
    protected $table = 'cash_sessions';
    protected $fillable = [
        'staff_id',
        'saldo_awal',
        'saldo_akhir',
        'penjualan_fnb',
        'waktu_buka',
        'waktu_tutup',
        'status',
    ];

    public function staff()
    {
        return $this->belongsTo(Admin::class, 'staff_id');
    }
}
