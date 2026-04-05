<?php

namespace App\Http\Controllers;

use App\Http\Requests\DecrementCartItemsRequest;
use App\Http\Requests\IncrementCartItemsRequest;
use App\Http\Requests\StoreCartItemsRequest;
use App\Models\CartItems;
use App\Services\CartItemsService;
use Gate;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CartItemsController extends Controller
{
    use AuthorizesRequests;
    protected $cartService;
    public function __construct(CartItemsService $cartService)
    {
        $this->cartService = $cartService;
    }
    public function index()
    {
        $items = CartItems::with('product')->get();
        return response()->json($items);
    }
    public function show($id)
    {
        $cartItem = CartItems::with('product')->findOrFail($id);
        $this->authorize('view', $cartItem);
        return response()->json($cartItem);
    }
    public function store(StoreCartItemsRequest $r)
    {
        $item = $this->cartService->store($r->validated());
        return response()->json($item, 201);
    }
    public function increment(IncrementCartItemsRequest $r, $id)
    {
        $item = $this->cartService->increment($id, $r->validated());
        $this->authorize('update', $item);
        return response()->json($item, 201);
    }
    public function decrement(DecrementCartItemsRequest $r, $id)
    {
        $cart = CartItems::findOrFail($id);
        $this->authorize('update', $cart);
        $item = $this->cartService->decrement($id, $r->validated());
        if (!$item) {
            return response()->json([
                'message' => 'Item removed from cart'
            ], 201);
        }
        return response()->json($item, 201);
    }
    public function destroy($id)
    {
        $item = CartItems::findOrFail($id);
        $this->authorize('delete', $item);
        $result = $this->cartService->destroy($id);
        return response()->json($result, 200);
    }
}