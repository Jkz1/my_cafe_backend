<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use App\Models\Coupons;
use App\Models\Order;
use App\Models\OrderDetails;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DataSeed extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user1 = User::factory()->create([
            'name' => 'User',
            'email' => 'User@gmail.com',
            'password' => bcrypt('password123'),
        ]);
        $user2 = User::factory()->create([
            'name' => 'User1',
            'email' => 'User1@gmail.com',
            'password' => bcrypt('password123'),
        ]);
        $admin = User::factory()->create([
            'name' => 'Admin',
            'email' => 'Admin@gmail.com',
            'password' => bcrypt('Admin123'),
        ]);
        $admin->assignRole('admin');

        $soft = Category::factory()->create([
            'name' => 'Soft Drink',
            'slug' => 'soft-drink',
        ]);
        $meal = Category::factory()->create([
            'name' => 'Meal',
            'slug' => 'meal',
        ]);
        $coffee = Category::factory()->create([
            'name' => 'Coffee',
            'slug' => 'coffee',
        ]);
        $pastry = Category::factory()->create([
            'name' => 'Pastry',
            'slug' => 'pastry',
        ]);

        $cocaCola = Product::factory()->create([
            'name' => 'Coca Cola',
            'slug' => 'coca-cola',
            'category_id' => $soft->id,
            'description' => 'A refreshing soft drink.',
            'price' => 1.99,
            'stock' => 50,
            'is_available' => true,
        ]);

        $sprite = Product::factory()->create([
            'name' => 'Sprite',
            'slug' => 'sprite',
            'category_id' => $soft->id,
            'description' => 'A refreshing lemon-lime soda.',
            'price' => 1.99,
            'stock' => 50,
            'is_available' => true,
        ]);

        $burger = Product::factory()->create([
            'name' => 'Beef Burger',
            'slug' => 'beef-burger',
            'category_id' => $meal->id,
            'description' => 'A delicious beef burger.',
            'price' => 5.99,
            'stock' => 20,
            'is_available' => true,
        ]);

        $clubSandwich = Product::factory()->create([
            'name' => 'Club Sandwich',
            'slug' => 'club-sandwich',
            'category_id' => $meal->id,
            'description' => 'Classic club sandwich with turkey and bacon.',
            'price' => 6.99,
            'stock' => 15,
            'is_available' => true,
        ]);

        $espresso = Product::factory()->create([
            'name' => 'Espresso',
            'slug' => 'espresso',
            'category_id' => $coffee->id,
            'description' => 'Strong and bold espresso shot.',
            'price' => 2.50,
            'stock' => 100,
            'is_available' => true,
        ]);

        $cappuccino = Product::factory()->create([
            'name' => 'Cappuccino',
            'slug' => 'cappuccino',
            'category_id' => $coffee->id,
            'description' => 'Espresso with steamed milk and thick foam.',
            'price' => 3.50,
            'stock' => 50,
            'is_available' => true,
        ]);

        $croissant = Product::factory()->create([
            'name' => 'Croissant',
            'slug' => 'croissant',
            'category_id' => $pastry->id,
            'description' => 'Flaky and buttery french croissant.',
            'price' => 2.99,
            'stock' => 30,
            'is_available' => true,
        ]);

        $muffin = Product::factory()->create([
            'name' => 'Blueberry Muffin',
            'slug' => 'blueberry-muffin',
            'category_id' => $pastry->id,
            'description' => 'Freshly baked blueberry muffin.',
            'price' => 2.49,
            'stock' => 25,
            'is_available' => true,
        ]);

        // Add Coupons
        $publicCoupon = Coupons::create([
            'name' => 'Welcome Discount',
            'code' => 'WELCOME10',
            'type' => 'percent',
            'value' => 10,
            'min_spend' => 5.00,
            'usage_limit' => 100,
            'user_limit' => 1,
            'is_active' => true,
            'starts_at' => now(),
            'expires_at' => now()->addDays(30),
        ]);

        $restrictedCoupon = Coupons::create([
            'name' => 'VIP Special',
            'code' => 'VIP20',
            'type' => 'fixed',
            'value' => 5.00,
            'min_spend' => 15.00,
            'usage_limit' => 50,
            'user_limit' => 1,
            'is_active' => true,
            'starts_at' => now(),
            'expires_at' => now()->addDays(30),
        ]);

        $restrictedCoupon->users()->attach($user2->id);

        // Add Orders
        $order1 = Order::create([
            'user_id' => $user1->id,
            'subtotal' => 7.98,
            'discount_amount' => 0,
            'total_price' => 7.98,
            'status' => 'completed',
        ]);

        OrderDetails::create([
            'order_id' => $order1->id,
            'product_id' => $cocaCola->id,
            'quantity' => 1,
            'unit_price' => $cocaCola->price,
        ]);

        OrderDetails::create([
            'order_id' => $order1->id,
            'product_id' => $burger->id,
            'quantity' => 1,
            'unit_price' => $burger->price,
        ]);

        // Order 2 with restricted coupon
        $order2 = Order::create([
            'user_id' => $user2->id,
            'coupon_id' => $restrictedCoupon->id,
            'subtotal' => 15.98,
            'discount_amount' => 5.00, // VIP20 is fixed $5 off
            'total_price' => 10.98,
            'status' => 'completed',
        ]);

        OrderDetails::create([
            'order_id' => $order2->id,
            'product_id' => $clubSandwich->id,
            'quantity' => 2,
            'unit_price' => $clubSandwich->price,
        ]);

        OrderDetails::create([
            'order_id' => $order2->id,
            'product_id' => $sprite->id,
            'quantity' => 1,
            'unit_price' => $sprite->price,
        ]);

        // Order 3 with public coupon
        $order3 = Order::create([
            'user_id' => $user1->id,
            'coupon_id' => $publicCoupon->id,
            'subtotal' => 6.00,
            'discount_amount' => 0.70,
            'total_price' => 6.30,
            'status' => 'pending',
        ]);

        OrderDetails::create([
            'order_id' => $order3->id,
            'product_id' => $cappuccino->id,
            'quantity' => 2,
            'unit_price' => $cappuccino->price,
        ]);
    }
}
