<?php

namespace App\Actions\Orders;

use App\Models\CartItems;

class ClearCart
{
    public function handle($data, $next)
    {
        // We only delete the items that were actually part of this order
        CartItems::whereIn('id', $data->cartItemIds)->delete();

        return $next($data);
    }
}