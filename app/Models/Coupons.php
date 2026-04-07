<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['name', 'code', 'type', 'value', 'min_spend', 'usage_limit', 'user_limit', 'is_active', 'starts_at', 'expires_at'])]
class Coupons extends Model
{
    use HasFactory, SoftDeletes;
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
