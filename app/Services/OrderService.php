<?php

namespace App\Services;

use App\DTOs\OrderData;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Pipeline;

class OrderService
{
    public function createOrder(int $userId, array $cartItemIds, ?int $coupon_id = null): Order
    {
        return DB::transaction(function () use ($userId, $cartItemIds, $coupon_id) {
            $orderData = new OrderData($userId, $cartItemIds, couponId: $coupon_id);

            return Pipeline::send($orderData)
                ->through([
                    // --- Computation (no DB writes) ---
                    \App\Actions\Orders\ValidateAndPrepareItems::class,  // validate & lock rows
                    \App\Actions\Orders\ProcessOrderDetails::class,      // compute subtotal, stage details
                    \App\Actions\Orders\ApplyCoupon::class,              // compute grandTotal / discount

                    // --- Persistence (single DB write point) ---
                    \App\Actions\Orders\SaveOrder::class,                // create order + details, decrement stock, clear cart
                ])
                ->then(fn ($data) => $data->order->fresh('details'));
        });
    }

    public function updateOrderStatus(Order $order, string $status): Order
    {
        return DB::transaction(function () use ($order, $status) {
            if ($status === 'cancelled' && $order->status !== 'cancelled') {
                foreach ($order->details as $detail) {
                    $detail->product->increment('stock', $detail->quantity);
                }
            }
            $order->update(['status' => $status]);
            return $order;
        });
    }
}
