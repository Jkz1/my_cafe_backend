<?php

namespace App\Actions\Orders;

class CreateOrderRecord {
    public function handle($data, $next) {
        $data->order = \App\Models\Order::create([
            'user_id' => $data->userId,
            'total_price' => 0,
            'status' => 'pending',
            'coupon_id' => $data->couponId
        ]);
        return $next($data);
    }
}