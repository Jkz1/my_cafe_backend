<?php

namespace App\Actions\Orders;

use App\Services\CouponsService;

class ApplyCoupon
{
    public function __construct(
        protected CouponsService $couponService
    ) {}

    public function handle($data, $next)
    {
        if (!empty($data->couponId)) {
            $result = $this->couponService->applyCoupon(
                $data->couponId,
                $data->subTotal,
                $data->userId
            );

            $data->grandTotal = $result['final_total'];
            $data->discountAmount = $result['discount_amount'];
            $data->couponId = $result['coupon']->id;
        }

        return $next($data);
    }
}
