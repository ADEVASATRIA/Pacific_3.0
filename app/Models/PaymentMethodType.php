<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentMethodType extends Model
{
    protected $fillable = [
        'name',
        'slug'
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    public function paymentMethods()
    {
        return $this->hasMany(PaymentMethod::class, 'payment_method_type_id');
    }
}