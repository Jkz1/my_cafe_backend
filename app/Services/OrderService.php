<?php

namespace App\Services;

use App\DTOs\OrderData;
use App\Models\CartItems;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Pipeline;

class OrderService
{
    public function createOrder(int $userId, array $cartItemIds): Order
    {
        return DB::transaction(function () use ($userId, $cartItemIds) {
            $orderData = new OrderData($userId, $cartItemIds);

            return Pipeline::send($orderData)
                ->through([
                    \App\Actions\Orders\ValidateAndPrepareItems::class,
                    \App\Actions\Orders\CreateOrderRecord::class,
                    \App\Actions\Orders\ProcessOrderDetails::class,
                    \App\Actions\Orders\ClearCart::class, // (Implementation omitted for brevity)
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