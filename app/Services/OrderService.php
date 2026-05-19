<?php

namespace App\Services;

use App\DTOs\OrderData;
use App\Models\Order;
use App\Actions\Orders\ValidateAndPrepareItems;
use App\Actions\Orders\ProcessOrderDetails;
use App\Actions\Orders\ApplyCoupon;
use App\Actions\Orders\SaveOrder;
use App\Jobs\SendOrderConfirmationJob;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Pipeline;
use Illuminate\Database\Eloquent\Builder;
use Log;

class OrderService
{
    public function createOrder(int $userId, array $cartItemIds, ?int $coupon_id = null): Order
    {
        $order = DB::transaction(function () use ($userId, $cartItemIds, $coupon_id) {
            $orderData = new OrderData($userId, $cartItemIds, couponId: $coupon_id);

            return Pipeline::send($orderData)
                ->through([
                        // --- Computation (no DB writes) ---
                    ValidateAndPrepareItems::class,  // validate & lock rows
                    ProcessOrderDetails::class,      // compute subtotal, stage details
                    ApplyCoupon::class,              // compute grandTotal / discount

                        // --- Persistence (single DB write point) ---
                    SaveOrder::class,                // create order + details, decrement stock, clear cart
                ])
                ->then(fn($data) => $data->order->fresh('details'));
        });

        SendOrderConfirmationJob::dispatch($order);

        return $order;
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

    /**
     * Applies subqueries to calculate total orders and spend for a User query.
     */
    public function userOrderStatsQuery(Builder $query, ?string $from = null, ?string $until = null): Builder
    {
        return $query->addSelect([
            'total_orders_count' => Order::selectRaw('count(*)')
                ->whereColumn('user_id', 'users.id')
                ->when($from, fn($q) => $q->whereDate('orders.created_at', '>=', $from))
                ->when($until, fn($q) => $q->whereDate('orders.created_at', '<=', $until)),

            'total_spend_sum' => Order::selectRaw('coalesce(sum(total_price), 0)')
                ->whereColumn('user_id', 'users.id')
                ->when($from, fn($q) => $q->whereDate('orders.created_at', '>=', $from))
                ->when($until, fn($q) => $q->whereDate('orders.created_at', '<=', $until)),
        ]);
    }
}
