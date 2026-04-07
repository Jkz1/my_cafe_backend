<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ProductApiTest extends TestCase
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
    public function test_index_product(): void
    {
        $pro1 = Product::factory()->create([
            'name' => 'Pro1',
            'price' => 12
        ]);
        $pro2 = Product::factory()->create([
            'name' => 'Pro2',
            'price' => 15
        ]);
        $response = $this->get("/api/products");

        $response->assertStatus(200)->assertJson([
            [
                'id' => $pro1->id,
                'name' => 'Pro1',
                'price' => 12
            ],
            [
                'id' => $pro2->id,
                'name' => 'Pro2',
                'price' => 15
            ]
        ]);
    }
    public function test_show_product(): void
    {
        $product = Product::factory()->create([
            'name' => 'Coffee Latte',
            'price' => 31
        ]);

        $response = $this->getJson("/api/products/{$product->id}");

        $response->assertStatus(200)
            ->assertJson([
                'id' => $product->id,
                'name' => 'Coffee Latte',
                'price' => 31
            ]);
    }
    public function test_it_returns_404_if_product_not_found()
    {
        $response = $this->getJson('/api/products/999');

        $response->assertStatus(404);
    }
    public function test_it_can_create_product()
    {

        $category = Category::factory()->create();
        $data = [
            'name' => 'Espresso',
            'category_id' => $category->id,
            'stock' => 100,
            "is_available" => true,
            'price' => 15000
        ];

        $response = $this->actingAs($this->admin, 'sanctum')->postJson('/api/products', $data);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Product created!',
                'data' => [
                    'name' => 'Espresso'
                ]
            ]);

        $this->assertDatabaseHas('products', [
            'name' => 'Espresso',
            'price' => 15000
        ]);
    }
    public function test_it_validates_create_product()
    {
        $response = $this->actingAs($this->admin, 'sanctum')->postJson('/api/products', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'price']);
    }
    public function test_it_can_delete_product()
    {
        $product = Product::factory()->create();

        $response = $this->actingAs($this->admin, 'sanctum')->deleteJson("/api/products/{$product->id}");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Product deleted successfully'
            ]);

        $this->assertDatabaseMissing('products', [
            'id' => $product->id
        ]);
    }
}
