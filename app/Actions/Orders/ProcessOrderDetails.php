<?php

namespace App\Actions\Orders;

class ProcessOrderDetails {
    public function handle($data, $next) {
        foreach ($data->cartItems as $item) {
            $subtotal = $item->product->price * $item->quantity;
            $data->subTotal += $subtotal;

            $data->order->details()->create([
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'unit_price' => $item->product->price
            ]);

            $item->product->decrement('stock', $item->quantity);
        }
        $data->order->update(['subtotal' => $data->subTotal]);
        $data->order->update(['total_price' => $data->subTotal]);
        return $next($data);
    }
}