<?php

namespace Tests\Unit\Services;

use App\Models\Coupons;
use App\Services\CouponsService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CouponServiceTest extends TestCase
{
    use RefreshDatabase;

    protected CouponsService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new CouponsService();
    }

    public function test_it_calculates_fixed_discount_correctly()
    {
        $user = \App\Models\User::factory()->create();
        $coupon = Coupons::factory()->create([
            'type' => 'fixed',
            'value' => 50.00,
            'min_spend' => 100
        ]);

        $result = $this->service->applyCoupon($coupon->id, 200.00, $user->id);

        $this->assertEquals(50.00, $result['discount_amount']);
        $this->assertEquals(150.00, $result['final_total']);
    }

    public function test_it_calculates_percent_discount_correctly()
    {
        $user = \App\Models\User::factory()->create();
        $coupon = Coupons::factory()->create([
            'type' => 'percent',
            'value' => 10.00,
        ]);

        $result = $this->service->applyCoupon($coupon->id, 500.00, $user->id);

        $this->assertEquals(50.00, $result['discount_amount']);
        $this->assertEquals(450.00, $result['final_total']);
    }

    public function test_it_throws_exception_for_expired_coupon()
    {
        $user = \App\Models\User::factory()->create();
        $coupon = Coupons::factory()->create([
            'expires_at' => Carbon::yesterday(),
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('This coupon has expired.');

        $this->service->applyCoupon($coupon->id, 100.00, $user->id);
    }

    public function test_it_throws_exception_when_usage_limit_reached()
    {
        $user = \App\Models\User::factory()->create();
        $coupon = Coupons::factory()->create([
            'usage_limit' => 5,
            'used_count' => 5,
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('maximum usage limit');

        $this->service->applyCoupon($coupon->id, 100.00, $user->id);
    }

    public function test_it_increments_usage_count()
    {
        $coupon = Coupons::factory()->create(['used_count' => 0]);

        $this->service->incrementUsage($coupon);

        $this->assertEquals(1, $coupon->fresh()->used_count);
    }

    public function test_it_allows_public_coupon_for_any_user()
    {
        $user = \App\Models\User::factory()->create();
        $coupon = Coupons::factory()->create(['type' => 'fixed', 'value' => 10]);


        $result = $this->service->applyCoupon($coupon->id, 100.00, $user->id);
        $this->assertEquals(90.00, $result['final_total']);
    }

    public function test_it_allows_assigned_coupon_for_eligible_user()
    {
        $user = \App\Models\User::factory()->create();
        $coupon = Coupons::factory()->create(['type' => 'fixed', 'value' => 10]);


        $coupon->users()->attach($user->id);

        $result = $this->service->applyCoupon($coupon->id, 100.00, $user->id);
        $this->assertEquals(90.00, $result['final_total']);
    }

    public function test_it_throws_exception_for_ineligible_user()
    {
        $assignedUser = \App\Models\User::factory()->create();
        $unassignedUser = \App\Models\User::factory()->create();

        $coupon = Coupons::factory()->create(['type' => 'fixed', 'value' => 10]);
        $coupon->users()->attach($assignedUser->id);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('You are not eligible to use this coupon.');


        $this->service->applyCoupon($coupon->id, 100.00, $unassignedUser->id);
    }
}