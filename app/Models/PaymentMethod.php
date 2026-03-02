<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    protected $fillable = [
        'id',
        'name',
        'img_thumbnail',
        'type',
        'provider',
        'is_active',
    ];

    public $incrementing = false;
    protected $keyType = 'int';

    public function purchases()
    {
        return $this->hasMany(Purchase::class, 'payment_method_id');
    }

    public function getBadgeHtml($value)
    {
        if ($value) {
            return '<span class="badge bg-success">YES</span>';
        }
        return '<span class="badge bg-danger">NO</span>';
    }
}
