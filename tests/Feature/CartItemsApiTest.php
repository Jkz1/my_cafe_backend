<?php

namespace Tests\Feature;

use Spatie\Permission\Models\Role;
use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\CartItems;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CartItemsApiTest extends TestCase
{
    use RefreshDatabase;
    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'admin']);
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $this->admin = $admin;
    }
    
    public function test_it_can_get_all_cart_items()
    {
        CartItems::factory()->count(3)->create();
        $response = $this->actingAs($this->admin, 'sanctum')->getJson('/api/cart-items');

        $response->assertStatus(200)
            ->assertJsonCount(3);
    }

    public function test_user_can_view_cart_item()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $item = CartItems::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);

        $response = $this->actingAs($user)
            ->getJson("/api/cart-items/{$item->id}");

        $response->assertStatus(200)
            ->assertJson([
                'id' => $item->id
            ]);
    }

    public function test_user_cannot_view_other_users_cart_item()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $item = CartItems::factory()->create([
            'user_id' => $otherUser->id
        ]);

        $response = $this->actingAs($user)
            ->getJson("/api/cart-items/{$item->id}");

        $response->assertStatus(403);
    }


    public function test_user_can_add_to_cart()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock' => 10]);

        $payload = [
            'product_id' => $product->id,
            'quantity' => 2
        ];

        $response = $this->actingAs($user)
            ->postJson('/api/cart-items', $payload);

        $response->assertStatus(201);
        $this->assertDatabaseHas('cart_items', [
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 2
        ]);
    }


    public function test_user_can_increment_cart_item()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock' => 10]);

        $item = CartItems::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 2
        ]);

        $response = $this->actingAs($user)
            ->patchJson("/api/cart-items/{$item->id}/increment", [
                'quantity' => 2
            ]);
        $response->assertStatus(201);

        $this->assertDatabaseHas('cart_items', [
            'id' => $item->id,
            'quantity' => 4
        ]);
    }


    public function test_user_can_decrement_cart_item()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $item = CartItems::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 5
        ]);

        $response = $this->actingAs($user)
            ->patchJson("/api/cart-items/{$item->id}/decrement", [
                'quantity' => 2
            ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('cart_items', [
            'id' => $item->id,
            'quantity' => 3
        ]);
    }


    public function test_decrement_deletes_item_if_quantity_zero()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $item = CartItems::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 2
        ]);

        $response = $this->actingAs($user)
            ->patchJson("/api/cart-items/{$item->id}/decrement", [
                'quantity' => 2
            ]);

        $response->assertStatus(201);

        $this->assertDatabaseMissing('cart_items', [
            'id' => $item->id,
        ]);
    }


    public function test_user_can_delete_cart_item()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $item = CartItems::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->id
        ]);

        $response = $this->actingAs($user)
            ->deleteJson("/api/cart-items/{$item->id}");

        $response->assertStatus(200);

        $this->assertDatabaseMissing('cart_items', [
            'id' => $item->id
        ]);
    }


    public function test_guest_cannot_access_cart()
    {
        $product = Product::factory()->create();

        $response = $this->postJson('/api/cart-items', [
            'product_id' => $product->id,
            'quantity' => 1
        ]);

        $response->assertStatus(401);
    }
}