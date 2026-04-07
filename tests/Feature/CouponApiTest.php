<?php

namespace Tests\Feature;

use App\Models\Coupons;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CouponApiTest extends TestCase
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

    public function test_admin_can_list_coupons()
    {
        Coupons::factory()->count(3)->create();

        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/coupons');

        $response->assertStatus(200)
            ->assertJsonCount(3);
    }

    public function test_admin_can_create_coupon()
    {
        $data = [
            'name' => 'Summer Sale',
            'code' => 'SUMMER2026',
            'type' => 'percent',
            'value' => 20,
            'is_active' => true,
            'starts_at' => now()->toDateTimeString(),
            'expires_at' => now()->addDays(10)->toDateTimeString(),
        ];

        $response = $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/coupons', $data);

        $response->assertStatus(201)
            ->assertJsonPath('data.code', 'SUMMER2026');

        $this->assertDatabaseHas('coupons', ['code' => 'SUMMER2026']);
    }

    public function test_create_coupon_fails_with_invalid_percentage()
    {
        $data = [
            'name' => 'Invalid Coupon',
            'code' => 'FAIL150',
            'type' => 'percent',
            'value' => 150, // Should fail because max is 100
        ];

        $response = $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/coupons', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['value']);
    }

    public function test_admin_can_delete_coupon()
    {
        $coupon = Coupons::factory()->create();

        $response = $this->actingAs($this->admin, 'sanctum')->deleteJson("/api/coupons/{$coupon->id}");
        
        $response->dump();
        $response->assertStatus(200);

        // If using soft deletes
        $this->assertSoftDeleted('coupons', ['id' => $coupon->id]);
    }
}