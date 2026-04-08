<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Guarded(['total_price','discount_amount','subtotal'])]
class Order extends Model
{
    use HasFactory;
    public function details()
    {
        return $this->hasMany(OrderDetails::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function coupon()
    {
        return $this->belongsTo(Coupons::class);
    }
}
