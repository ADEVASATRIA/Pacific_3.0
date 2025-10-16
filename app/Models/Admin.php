<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Purchase;

class Admin extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $table = 'admins';

    protected $fillable = [
        'name',
        'username',
        'password',
        'pin',
        'is_admin',
        'is_root',
        'is_guest',
        'is_staff',
        'is_active',
        'created_by',
        'phone',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'pin',
        'api_token',
    ];

    protected $casts = [
        'is_admin' => 'boolean',
        'is_root' => 'boolean',
        'is_guest' => 'boolean',
        'is_staff' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function purchase()
    {
        return $this->hasMany(Purchase::class);
    }

    public function getBadgeHtml($value)
    {
        if ($value) {
            return '<span class="badge bg-success">YES</span>';
        }
        return '<span class="badge bg-danger">NO</span>';
    }
}
