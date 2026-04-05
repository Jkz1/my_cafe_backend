<?php

namespace Tests\Feature;

use App\Models\CartItems;
use App\Services\OrderService;
use Spatie\Permission\Models\Role;
use Tests\TestCase;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderApiTest extends TestCase
{
    use RefreshDatabase;
    protected function createAdminUser()
    {
        // 2. Create Roles and Assign Permissions
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $user = User::factory()->create();
        $user->assignRole('admin');

        return $user;
    }
    public function test_it_can_get_all_orders()
    {
        Order::factory()->count(3)->create();
        $admin = $this->createAdminUser();
        $response = $this->actingAs($admin, 'sanctum')->getJson('/api/orders');

        $response->assertStatus(200)
            ->assertJsonCount(3);
    }

    public function test_user_can_view_their_own_order()
    {
        $user = User::factory()->create();
        $order = Order::factory()->for($user)->create();

        $response = $this->actingAs($user)
            ->getJson("/api/orders/{$order->id}");

        $response->assertStatus(200)
            ->assertJson([
                'id' => $order->id
            ]);
    }
    public function test_user_cannot_view_others_order()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $order = Order::factory()->for($otherUser)->create();

        $response = $this->actingAs($user)
            ->getJson("/api/orders/{$order->id}");

        $response->assertStatus(403);
    }
    public function test_it_returns_404_if_order_not_found()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->getJson('/api/orders/999');

        $response->assertStatus(404);
    }

    public function test_user_can_get_their_own_orders()
    {
        $user = User::factory()->create();

        Order::factory()->count(2)->for($user)->create();
        Order::factory()->count(1)->create(); // other user

        $response = $this->actingAs($user)
            ->getJson('/api/orders/my-orders');

        $response->assertStatus(200)
            ->assertJsonCount(2);
    }
    public function test_user_can_create_order()
    {
        $user = User::factory()->create();

        $products = Product::factory()->count(2)->create();

        $payload = [
            'items' => [
                ['cart_item_id' => CartItems::factory()->for($user)->for($products[0])->create()->id],
                ['cart_item_id' => CartItems::factory()->for($user)->for($products[1])->create()->id]
            ]
        ];

        $response = $this->actingAs($user)
            ->postJson('/api/orders', $payload);

        $response->assertStatus(201)
            ->assertJsonStructure(['message', 'id']);

        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id
        ]);
    }
    public function test_it_validates_create_order()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->postJson('/api/orders', []);

        $response->assertStatus(422);
    }
    public function test_it_returns_400_if_service_fails_on_create()
    {
        $this->mock(OrderService::class, function ($mock) {
            $mock->shouldReceive('createOrder')
                ->andThrow(new \Exception('Something went wrong'));
        });

        $user = User::factory()->create();
        $products = Product::factory()->count(2)->create();
        $payload = [
            'items' => [
                ['cart_item_id' => CartItems::factory()->for($user)->for($products[0])->create()->id],
                ['cart_item_id' => CartItems::factory()->for($user)->for($products[1])->create()->id]
            ]
        ];
        $response = $this->actingAs($user)
            ->postJson('/api/orders', $payload);

        $response->assertStatus(400)
            ->assertJson([
                'error' => 'Something went wrong'
            ]);
    }
    public function test_it_can_update_order_status()
    {
        $user = User::factory()->create();
        $order = Order::factory()->for($user)->create();
        $admin = $this->createAdminUser();
        $response = $this->actingAs($admin)
            ->putJson("/api/orders/{$order->id}", [
                'status' => 'completed'
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Order updated!',
                'id' => $order->id
            ]);
    }
    public function test_it_returns_400_if_update_fails()
    {
        $this->mock(OrderService::class, function ($mock) {
            $mock->shouldReceive('updateOrderStatus')
                ->andThrow(new \Exception('Update failed'));
        });

        $user = User::factory()->create();
        $order = Order::factory()->for($user)->create();
        $admin = $this->createAdminUser();

        $response = $this->actingAs($admin)
            ->putJson("/api/orders/{$order->id}", [
                'status' => 'completed'
            ]);

        $response->assertStatus(400)
            ->assertJson([
                'error' => 'Update failed'
            ]);
    }
}