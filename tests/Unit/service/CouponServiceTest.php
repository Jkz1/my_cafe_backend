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
        $coupon = Coupons::factory()->create([
            'type' => 'fixed',
            'value' => 50.00,
            'min_spend' => 100
        ]);

        $result = $this->service->applyCoupon($coupon->id, 200.00);

        $this->assertEquals(50.00, $result['discount_amount']);
        $this->assertEquals(150.00, $result['final_total']);
    }

    public function test_it_calculates_percent_discount_correctly()
    {
        $coupon = Coupons::factory()->create([
            'type' => 'percent',
            'value' => 10.00, // 10%
        ]);

        $result = $this->service->applyCoupon($coupon->id, 500.00);

        $this->assertEquals(50.00, $result['discount_amount']);
        $this->assertEquals(450.00, $result['final_total']);
    }

    public function test_it_throws_exception_for_expired_coupon()
    {
        $coupon = Coupons::factory()->create([
            'expires_at' => Carbon::yesterday(),
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('This coupon has expired.');

        $this->service->applyCoupon($coupon->id, 100.00);
    }

    public function test_it_throws_exception_when_usage_limit_reached()
    {
        $coupon = Coupons::factory()->create([
            'usage_limit' => 5,
            'used_count' => 5,
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('maximum usage limit');

        $this->service->applyCoupon($coupon->id, 100.00);
    }

    public function test_it_increments_usage_count()
    {
        $coupon = Coupons::factory()->create(['used_count' => 0]);

        $this->service->incrementUsage($coupon);

        $this->assertEquals(1, $coupon->fresh()->used_count);
    }
}