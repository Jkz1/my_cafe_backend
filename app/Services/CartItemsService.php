<?php
namespace App\Services;

use App\Models\CartItems;
use App\Models\Product;
use Auth;
use Exception;

class CartItemsService
{
    public function increment($cartItemId, $quantity = 1)
    {
        $user = Auth::user();
        $item = CartItems::where("id", $cartItemId)
            ->where("user_id", $user->id)
            ->firstOrFail();
        if ($item->product->stock < ($item->quantity + 1)) {
            throw new Exception("Stock not enough");
        }
        $item->increment("quantity", $quantity);
        return $item->fresh();
    }
    public function decrement($cartItemId, $quantity = 1)
    {
        $user = Auth::user();
        $item = CartItems::where("id", $cartItemId)
            ->where("user_id", $user->id)
            ->firstOrFail();
        if ($item->quantity <= $quantity) {
            $item->delete();
            return null;
        }
        $item->decrement("quantity", $quantity);
        return $item->fresh();
    }

    public function store($productId, $quantity = 1)
    {
        $user = Auth::user();

        $product = Product::findOrFail($productId);

        $item = CartItems::where("product_id", $productId)
            ->where("user_id", $user->id)
            ->first();

        if ($item) {
            if ($product->stock < ($item->quantity + $quantity)) {
                throw new Exception("Stock not enough");
            }

            $item->increment("quantity", $quantity);
            return $item->fresh();
        }

        if ($product->stock < $quantity) {
            throw new Exception("Stock not enough");
        }

        return CartItems::create([
            "user_id" => $user->id,
            "product_id" => $productId,
            "quantity" => $quantity
        ]);
    }

    public function destroy($cartItemId)
    {
        $user = Auth::user();
        $item = CartItems::where("id", $cartItemId)
            ->where("user_id", $user->id)
            ->firstOrFail();
        $item->delete();
        return [
            "message" => "Cart item deleted"
        ];
    }
}