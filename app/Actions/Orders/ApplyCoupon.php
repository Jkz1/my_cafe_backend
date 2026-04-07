<?php

namespace App\Actions\Orders;

use App\Services\CouponsService;

class ApplyCoupon
{
    public function __construct(
        protected CouponsService $couponService
    ) {
    }
    public function handle($data, $next)
    {
        if (!empty($data->couponId)) {
            // No try/catch here! 
            // If applyCoupon() throws an exception, the whole chain stops,
            // and your Controller can catch it to show the error message to the user.
            $result = $this->couponService->applyCoupon(
                $data->couponId,
                $data->subTotal,
            );

            $data->grandTotal = $result['final_total'];
            $data->discountAmount = $result['discount_amount'];
            $data->couponId = $result['coupon']->id;
            $data->order->update(['total_price' => $data->grandTotal]);

        }

        return $next($data);
    }
}