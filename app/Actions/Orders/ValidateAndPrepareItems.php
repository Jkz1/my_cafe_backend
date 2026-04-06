<?php

namespace App\Actions\Orders;

class ValidateAndPrepareItems {
    public function handle($data, $next) {
        $data->cartItems = \App\Models\CartItems::with('product')
            ->whereIn('id', $data->cartItemIds)
            ->where('user_id', $data->userId)
            ->lockForUpdate()
            ->get();

        if ($data->cartItems->count() !== count($data->cartItemIds)) {
            throw new \Exception("Invalid cart items.");
        }

        foreach ($data->cartItems as $item) {
            if ($item->product->stock < $item->quantity) {
                throw new \Exception("Product {$item->product->name} is out of stock.");
            }
        }
        return $next($data);
    }
}