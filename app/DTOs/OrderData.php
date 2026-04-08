<?php

namespace App\DTOs;

class OrderData {
    public function __construct(
        public int $userId,
        public array $cartItemIds,
        public $cartItems = null,
        public ?int $discountAmount = null,
        public ?int $couponId = null,
        public float $subTotal = 0,
        public float $grandTotal = 0,
        public array $pendingOrderDetails = [] 
    ) {}
}
