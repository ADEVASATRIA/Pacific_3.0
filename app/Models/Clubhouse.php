<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Clubhouse extends Model
{
    protected $table = 'clubhouses';
    protected $fillable = ['name', 'location', 'phone'];

    public function customer()
    {
        return $this->hasMany(Customer::class);
    }
}
