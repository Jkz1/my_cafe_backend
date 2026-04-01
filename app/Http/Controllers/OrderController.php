<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use DB;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('details.product')->get();
        return response()->json($orders);
    }
    public function show($id)
    {
        $order = Order::findOrFail($id)->with('details.product')->first();
        return response()->json($order);
    }
    public function myOrders()
    {
        $user = auth()->user();
        $orders = Order::where('user_id', $user->id)->with('details.product')->get();
        return response()->json($orders);
    }
    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);
        return DB::transaction(function () use ($request) {
            $order = Order::create([
                'user_id' => auth()->id(),
                'total_price' => 0,
                'status' => 'pending',
            ]);
            $grandTotal = 0;
            $productIds = collect($request->items)->pluck('product_id');
            $products = Product::whereIn('id', $productIds)->get()->keyBy('id');
            foreach ($request->items as $item) {
                $product = $products->get($item['product_id']);
                $subtotal = $product->price * $item['quantity'];
                $grandTotal += $subtotal;
                $order->details()->create([
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $product->price,
                ]);
            }
            $order->update(['total_price' => $grandTotal]);

            return response()->json(['message' => 'Order created!', 'id' => $order->id]);
        });
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,shipping,completed,cancelled',
        ]);
        $order = Order::findOrFail($id);
        $order->update([
            'status' => $request->status,
        ]);
        return response()->json([
            'message' => 'Order updated!',
            'id' => $order->id
        ]);

    }
    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        $order->delete();
        return response()->json(['message' => 'Order deleted!']);
    }
}
