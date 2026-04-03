<?php

namespace App\Services;

use App\Models\CartItems;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Exception;

class OrderService
{
    public function createOrder(int $userId, array $cartItemIds): Order
    {
        return DB::transaction(function () use ($userId, $cartItemIds) {

            $cartItems = CartItems::with('product')
                ->whereIn('id', $cartItemIds)
                ->where('user_id', $userId)
                ->lockForUpdate()
                ->get();

            if ($cartItems->count() !== count($cartItemIds)) {
                throw new Exception("Invalid cart items.");
            }

            $order = Order::create([
                'user_id' => $userId,
                'total_price' => 0,
                'status' => 'pending',
            ]);

            $grandTotal = 0;

            foreach ($cartItems as $item) {
                $product = $item->product;
                if ($product->stock < $item->quantity) {
                    throw new Exception("Product {$product->name} is out of stock.");
                }
                $subtotal = $product->price * $item->quantity;
                $grandTotal += $subtotal;
                $order->details()->create([
                    'product_id' => $product->id,
                    'quantity' => $item->quantity,
                    'unit_price' => $product->price
                ]);

                $product->decrement('stock', $item->quantity);
            }

            $order->update(['total_price' => $grandTotal]);

            CartItems::whereIn('id', $cartItemIds)->delete();

            return $order->fresh('details');
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