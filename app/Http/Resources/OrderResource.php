<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'coupon_id' => $this->coupon_id,
            'total_price' => $this->total_price,
            'subtotal' => $this->subtotal,
            'discount_amount' => $this->discount_amount,
            'status' => $this->status,
            'payment_method' => $this->payment_method,
            'shipping_address' => $this->shipping_address,
            'user' => new UserResource($this->whenLoaded('user')),
            'coupon' => new CouponResource($this->whenLoaded('coupon')),
            'details' => OrderDetailResource::collection($this->whenLoaded('details')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
