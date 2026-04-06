<?php

namespace App\DTOs;

class OrderData {
    public function __construct(
        public int $userId,
        public array $cartItemIds,
        public $cartItems = null,
        public $order = null,
        public float $grandTotal = 0
    ) {}
}