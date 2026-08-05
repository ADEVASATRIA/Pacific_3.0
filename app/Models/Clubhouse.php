<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Clubhouse extends Model
{
    protected $table = 'clubhouses';
    protected $fillable = ['name', 'location', 'phone', 'dokumen_pdf', 'ktp_pengurus'];

    public function customer()
    {
        return $this->hasMany(Customer::class);
    }
}
