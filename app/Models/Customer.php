<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
            'id',
            'name',
            'phone',
            'dob',
            'awal_masa_berlaku',
            'akhir_masa_berlaku',
            'is_pelatih',
            'clubhouse_id',
            'tipe_customer',
            'kategory_customer',
            'id_club_renang',
            'catatan',
            'member_id',
    ];
    
    public function clubhouse()
    {
        return $this->belongsTo(Clubhouse::class, 'id_club_renang', 'id');
    }

    public function clubhouse2()
    {
        return $this->belongsTo(Clubhouse::class, 'clubhouse_id', 'id');
    }
    
    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'customer_id');
    }
    
    public function tiketTerbaru()
    {
        return $this->hasOne(Ticket::class, 'customer_id')
                ->where('code', 'LIKE', 'M%')
                ->latest('created_at'); // ambil ticket terbaru by created_at desc
    }

    public function generateMemberId()
    {
        $ctr = 0;
        $search_limit = 3;
        $length = 6; // panjang angka random

        while (true) {
            $member_id = 'PP' . \Carbon\Carbon::now()->format('y') . str_pad(random_int(1, 999999), $length, '0', STR_PAD_LEFT);

            if (self::where('member_id', $member_id)->exists()) {
                $ctr++;
                if ($ctr > $search_limit) {
                    $ctr = 0;
                    $length++; // kalau mentok, tambah panjang digit
                }
            } else {
                break;
            }
        }

        return $member_id;
    }
}
