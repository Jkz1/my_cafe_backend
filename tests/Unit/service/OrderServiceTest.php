<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\CartItems;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new OrderService();
    }
    public function test_it_can_create_order_successfully()
    {
        $user = User::factory()->create();

        $product = Product::factory()->create([
            'price' => 100,
            'stock' => 10
        ]);

        $cartItem = CartItems::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 2
        ]);

        $order = $this->service->createOrder($user->id, [$cartItem->id]);

        // Order created
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'user_id' => $user->id,
            'total_price' => 200
        ]);

        // Order detail created
        $this->assertDatabaseHas('order_details', [
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 2
        ]);

        // Stock reduced
        $this->assertEquals(8, $product->fresh()->stock);

        // Cart deleted
        $this->assertDatabaseMissing('cart_items', [
            'id' => $cartItem->id
        ]);
    }
    public function test_it_throws_exception_if_cart_items_invalid()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid cart items.');

        $user = User::factory()->create();

        // No cart item created → invalid
        $this->service->createOrder($user->id, [999]);
    }
    public function test_it_throws_exception_if_stock_not_enough()
    {
        $this->expectException(\Exception::class);

        $user = User::factory()->create();

        $product = Product::factory()->create([
            'stock' => 1
        ]);

        $cartItem = CartItems::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 5
        ]);

        $this->service->createOrder($user->id, [$cartItem->id]);
    }
    public function test_it_rolls_back_transaction_if_failed()
    {
        try {
            $user = User::factory()->create();

            $product = Product::factory()->create([
                'stock' => 1
            ]);

            $cartItem = CartItems::factory()->create([
                'user_id' => $user->id,
                'product_id' => $product->id,
                'quantity' => 5
            ]);

            $this->service->createOrder($user->id, [$cartItem->id]);
        } catch (\Exception $e) {
            // ignore
        }

        // Order should NOT exist
        $this->assertDatabaseCount('orders', 0);

        // Stock should NOT change
        $this->assertEquals(1, Product::first()->stock);

        // Cart should still exist
        $this->assertDatabaseCount('cart_items', 1);
    }
    public function test_it_can_update_order_status()
    {
        $order = Order::factory()->create([
            'status' => 'pending'
        ]);

        $updated = $this->service->updateOrderStatus($order, 'completed');

        $this->assertEquals('completed', $updated->status);
    }
    public function test_it_restores_stock_when_order_cancelled()
    {
        $product = Product::factory()->create([
            'stock' => 5
        ]);

        $order = Order::factory()->create([
            'status' => 'pending'
        ]);

        $order->details()->create([
            'product_id' => $product->id,
            'quantity' => 2,
            'unit_price' => 100
        ]);

        $this->service->updateOrderStatus($order->fresh('details.product'), 'cancelled');

        // Stock restored
        $this->assertEquals(7, $product->fresh()->stock);
    }
    public function test_cancelling_twice_does_not_double_restore_stock()
    {
        $product = Product::factory()->create([
            'stock' => 5
        ]);

        $order = Order::factory()->create([
            'status' => 'cancelled'
        ]);

        $order->details()->create([
            'product_id' => $product->id,
            'quantity' => 2,
            'unit_price' => 100
        ]);

        $this->service->updateOrderStatus($order->fresh('details.product'), 'cancelled');

        // Stock should remain same
        $this->assertEquals(5, $product->fresh()->stock);
    }
}