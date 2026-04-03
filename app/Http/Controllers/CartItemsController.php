<?php

namespace App\Http\Controllers;

use App\Http\Requests\DecrementCartItemsRequest;
use App\Http\Requests\IncrementCartItemsRequest;
use App\Http\Requests\StoreCartItemsRequest;
use App\Models\CartItems;
use App\Services\CartItemsService;
use Gate;

class CartItemsController extends Controller
{
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
        Gate::authorize('view', $cartItem);
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
        return response()->json($item, 201);
    }
    public function decrement(DecrementCartItemsRequest $r, $id)
    {
        $item = $this->cartService->decrement($id, $r->validated());
        if (!$item) {
            return response()->json([
                'message' => 'Item removed from cart'
            ]);
        }
        return response()->json($item);
    }
    public function destroy($id)
    {
        $result = $this->cartService->destroy($id);
        return response()->json($result);
    }
}