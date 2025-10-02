<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Promo extends Model
{
    protected $fillable = [
           'code',
           'type',
           'value',
           'max_discount',
           'min_purchase',
           'quota',
           'description',
           'expired_date',
           'start_date',
           'is_active',
           'ticket_types',
    ];
    public function purchases() {
        return $this->hasMany(Purchase::class);
    }
   
    public function setTicketTypesAttribute($value)
    {
        $this->attributes['ticket_types'] = json_encode($value);
    }
   
    public function getTicketTypesAttribute($value)
    {
        return json_decode($value);
    }
}
