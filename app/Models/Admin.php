<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    protected $fillable = [
		'name',
		'username',
		'password',
		'pin',
		'is_admin',
		'is_root',
		'is_guest',
		'is_active',
		'is_staff',
		'created_by'
	];
	public function purchase()
	{
		return $this->hasMany(Purchase::class);
	}
}
