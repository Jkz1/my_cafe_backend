<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\StoreOrderRequest;
use App\Http\Requests\Order\UpdateOrderRequest;
use App\Models\Order;
use App\Services\OrderService;
use Exception;
use Gate;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class OrderController extends Controller
{
    use AuthorizesRequests;
    protected $orderService;
    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }
    public function index()
    {
        $orders = Order::with('details.product')->get();
        return response()->json($orders);
    }
    public function show($id)
    {
        $order = Order::with('details.product')->findOrFail($id);
        $this->authorize('view', $order);
        return response()->json($order);
    }
    public function myOrders()
    {
        $orders = auth()->user()->orders()->with('details.product')->get();
        return response()->json($orders);
    }
    public function store(StoreOrderRequest $request)
    {
        try {
            $order = $this->orderService->createOrder(
                auth()->id(), 
                $request->validated()['items'],
                $request->validated()['coupon_id'] ?? null,
            );
            return response()->json([
                'message' => 'Order created!', 
                'id' => $order->id
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 400);
        }
    }
    public function update(UpdateOrderRequest $request, $id)
    {
        try{
            $order = Order::with('details.product')->findOrFail($id);
            $order = $this->orderService->updateOrderStatus($order, $request->validated()['status']);
            return response()->json([
                'message' => 'Order updated!',
                'id' => $order->id
            ]);
        }catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 400);
        }
    }
}
