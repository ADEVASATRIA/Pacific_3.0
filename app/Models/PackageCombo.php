<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PackageCombo extends Model
{
    protected $fillable = [
        'name',
        'price',
        'expired_duration',
        'tipe_khusus',
        'is_Active',
    ];
    public function details() {
        return $this->hasMany(PackageComboDetail::class);
    }
    
    public function packageComboRedeem() {
        return $this->hasMany(PackageComboRedeem::class);
    }
}
