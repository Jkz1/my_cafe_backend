<?php

namespace Tests\Unit\Services;

use App\Models\Coupons;
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
    public function test_it_cannot_create_order_with_other_user_cart_item()
    {
        $otherUser = User::factory()->create();
        $user = User::factory()->create();

        $product = Product::factory()->create([
            'price' => 100,
            'stock' => 10
        ]);

        $cartItem = CartItems::factory()->create([
            'user_id' => $otherUser->id, 
            'product_id' => $product->id,
            'quantity' => 2
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Invalid cart items.");

        
        try {
            $this->service->createOrder($user->id, [$cartItem->id]);
        } finally {
            $this->assertDatabaseCount('orders', 0);

            $this->assertEquals(10, $product->fresh()->stock);
            
            $this->assertDatabaseHas('cart_items', [
                'id' => $cartItem->id,
                'user_id' => $otherUser->id
            ]);
        }
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
    /**
     * Test successful order creation with a coupon.
     */
    public function test_it_applies_coupon_successfully_and_reduces_total()
    {
        $user = User::factory()->create();

        // Create a $50 fixed discount coupon
        $coupon = Coupons::factory()->create([
            'code' => 'SAVE50',
            'type' => 'fixed',
            'value' => 50,
            'is_active' => true,
            'min_spend' => 100
        ]);

        $product = Product::factory()->create([
            'price' => 200,
            'stock' => 10
        ]);

        $cartItem = CartItems::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 2 // Subtotal = 400
        ]);

        // Pass the coupon code to the service
        $order = $this->service->createOrder($user->id, [$cartItem->id], $coupon->id);

        // Total should be 400 - 50 = 350
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'total_price' => 350,
            'coupon_id' => $coupon->id
        ]);

        // Ensure usage count was incremented
        $this->assertEquals(1, $coupon->fresh()->used_count);
    }

    /**
     * Test order fails if coupon is invalid (e.g., expired or below min spend).
     */
    public function test_it_throws_exception_if_coupon_is_invalid()
    {
        $user = User::factory()->create();

        // Create a coupon with a high min spend
        $coupon = Coupons::factory()->create([
            'code' => 'BIGSPENDER',
            'min_spend' => 1000,
            'is_active' => true
        ]);

        $product = Product::factory()->create(['price' => 100]); // Total is only 100

        $cartItem = CartItems::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 1
        ]);

        $this->expectException(\Exception::class);
        // The message comes from your CouponService validation
        $this->expectExceptionMessage("You must spend at least 1000");

        $this->service->createOrder($user->id, [$cartItem->id], $coupon->id);

        // Verify database remains clean due to transaction rollback
        $this->assertDatabaseCount('orders', 0);
        $this->assertEquals(0, $coupon->fresh()->used_count);
    }

    // /**
    //  * Test percentage-based coupon calculation.
    //  */
    public function test_it_calculates_percentage_discount_correctly()
    {
        $user = User::factory()->create();

        $coupon = Coupons::factory()->create([
            'code' => 'PERCENT10',
            'type' => 'percent',
            'value' => 10, // 10% off
            'is_active' => true
        ]);

        $product = Product::factory()->create(['price' => 500]);
        $cartItem = CartItems::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 1
        ]);

        $order = $this->service->createOrder($user->id, [$cartItem->id], $coupon->id);

        // 500 - (10% of 500) = 450
        $this->assertEquals(450, $order->total_price);
    }
}