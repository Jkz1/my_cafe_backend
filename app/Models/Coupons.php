<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Guarded([])]
class Coupons extends Model
{
    use HasFactory, SoftDeletes;
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
    public function users()
    {
        return $this->belongsToMany(User::class, 'coupon_user', 'coupon_id', 'user_id');
    }
}
