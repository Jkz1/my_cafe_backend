<?php
namespace App\Services;

use App\Models\Coupons;
use Carbon\Carbon;
use Exception;
use Str;

class CouponsService
{

    public function store(array $data): Coupons
    {

        if (empty($data['code'])) {
            $data['code'] = strtoupper(Str::random(10));
        }


        $data['starts_at'] = $data['starts_at'] ?? now();

        return Coupons::create($data);
    }
    public function applyCoupon(int $couponId, float $orderTotal): array
    {
        $coupon = Coupons::find($couponId);

        if (!$coupon) {
            throw new Exception("Invalid coupon code.");
        }

        $this->validateCoupon($coupon, $orderTotal);

        $discountAmount = $this->calculateDiscount($coupon, $orderTotal);
        $finalTotal = max(0, $orderTotal - $discountAmount);
        $coupon->increment('used_count');
        $coupon->save();

        return [
            'coupon' => $coupon,
            'discount_amount' => $discountAmount,
            'final_total' => $finalTotal,
        ];
    }
    protected function validateCoupon(Coupons $coupon, float $orderTotal): void
    {

        if (!$coupon->is_active) {
            throw new Exception("This coupon is no longer active.");
        }

        $now = Carbon::now();
        if ($coupon->starts_at && $now->lt($coupon->starts_at)) {
            throw new Exception("This coupon promotion hasn't started yet.");
        }
        if ($coupon->expires_at && $now->gt($coupon->expires_at)) {
            throw new Exception("This coupon has expired.");
        }
        if ($coupon->usage_limit !== null && $coupon->used_count >= $coupon->usage_limit) {
            throw new Exception("This coupon has reached its maximum usage limit.");
        }
        if ($coupon->min_spend !== null && $orderTotal < $coupon->min_spend) {
            throw new Exception("You must spend at least {$coupon->min_spend} to use this coupon.");
        }
    }

    /**
     * Calculate the actual discount value.
     */
    protected function calculateDiscount(Coupons $coupon, float $orderTotal): float
    {
        if ($coupon->type === 'percent') {
            return ($orderTotal * ($coupon->value / 100));
        }

        return (float) $coupon->value;
    }

    /**
     * Increment the usage counter after a successful order.
     */
    public function incrementUsage(Coupons $coupon): void
    {
        $coupon->increment('used_count');
    }
}