<?php

namespace App\Actions\Orders;

class ProcessOrderDetails
{
    public function handle($data, $next)
    {
        foreach ($data->cartItems as $item) {
            $subtotal = $item->product->price * $item->quantity;
            $data->subTotal += $subtotal;


            $data->pendingOrderDetails[] = [
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'unit_price' => $item->product->price,
            ];
        }
        $data->grandTotal = $data->subTotal;

        return $next($data);
    }
}
