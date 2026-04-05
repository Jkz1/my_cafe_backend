<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\CartItems;
use App\Services\CartItemsService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CartItemsServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new CartItemsService();
    }

    
    public function test_it_can_store_new_cart_item()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock' => 10]);

        $this->actingAs($user);

        $result = $this->service->store([
            'product_id' => $product->id,
            'quantity' => 2
        ]);

        $this->assertDatabaseHas('cart_items', [
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 2
        ]);
    }

    
    public function test_it_increments_existing_cart_item()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock' => 10]);

        $this->actingAs($user);

        $item = CartItems::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 2
        ]);

        $result = $this->service->store([
            'product_id' => $product->id,
            'quantity' => 3
        ]);

        $this->assertEquals(5, $result->quantity);
    }

    
    public function test_it_throws_exception_if_stock_not_enough_on_store()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Stock not enough');

        $user = User::factory()->create();
        $product = Product::factory()->create(['stock' => 2]);

        $this->actingAs($user);

        $this->service->store([
            'product_id' => $product->id,
            'quantity' => 5
        ]);
    }

    
    public function test_it_can_increment_cart_item()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock' => 10]);

        $this->actingAs($user);

        $item = CartItems::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 2
        ]);

        $result = $this->service->increment($item->id, ['quantity' => 2]);

        $this->assertEquals(4, $result->quantity);
    }

    
    public function test_it_throws_exception_if_stock_not_enough_on_increment()
    {
        $this->expectException(\Exception::class);

        $user = User::factory()->create();
        $product = Product::factory()->create(['stock' => 3]);

        $this->actingAs($user);

        $item = CartItems::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 3
        ]);

        $this->service->increment($item->id, ['quantity' => 1]);
    }

    
    public function test_it_can_decrement_cart_item()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $this->actingAs($user);

        $item = CartItems::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 5
        ]);

        $result = $this->service->decrement($item->id, ['quantity' => 2]);

        $this->assertEquals(3, $result->quantity);
    }

    
    public function test_it_deletes_item_if_quantity_becomes_zero()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $this->actingAs($user);

        $item = CartItems::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 2
        ]);

        $result = $this->service->decrement($item->id, ['quantity' => 2]);

        $this->assertNull($result);

        $this->assertDatabaseMissing('cart_items', [
            'id' => $item->id
        ]);
    }

    
    public function test_it_can_delete_cart_item()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $this->actingAs($user);

        $item = CartItems::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->id
        ]);

        $result = $this->service->destroy($item->id);

        $this->assertEquals('Cart item deleted', $result['message']);

        $this->assertDatabaseMissing('cart_items', [
            'id' => $item->id
        ]);
    }
}