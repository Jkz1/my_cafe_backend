<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['user_id', 'total_price', 'status'])]
class Order extends Model
{
    public function details()
    {
        return $this->hasMany(OrderDetails::class);
    }
}
