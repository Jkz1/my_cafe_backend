<?php

namespace App\Actions\Orders;

use App\Models\CartItems;
use App\Models\Order;

class SaveOrder
{
    public function handle($data, $next)
    {
        
        $order = (new Order)->forceFill([
            'user_id'         => $data->userId,
            'status'          => 'pending',
            'coupon_id'       => $data->couponId,
            'subtotal'        => $data->subTotal,
            'total_price'     => $data->grandTotal,
            'discount_amount' => $data->discountAmount,
        ]);
        $order->save();

        $order->details()->createMany($data->pendingOrderDetails);
        
        foreach ($data->cartItems as $item) {
            $item->product->decrement('stock', $item->quantity);
        }

        CartItems::whereIn('id', $data->cartItemIds)->delete();

        $data->order = $order;

        return $next($data);
    }
}
