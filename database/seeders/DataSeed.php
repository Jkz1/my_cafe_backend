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
        // Create Admin
        $admin = User::firstOrCreate(['email' => 'Admin@gmail.com'], [
            'name' => 'Admin',
            'password' => bcrypt('Admin123'),
        ]);
        $admin->assignRole('admin');

        // Create 4 random users
        $users = User::factory(4)->create();

        $soft = Category::firstOrCreate(['slug' => 'soft-drink'], [
            'name' => 'Soft Drink',
        ]);
        $meal = Category::firstOrCreate(['slug' => 'meal'], [
            'name' => 'Meal',
        ]);
        $coffee = Category::firstOrCreate(['slug' => 'coffee'], [
            'name' => 'Coffee',
        ]);
        $pastry = Category::firstOrCreate(['slug' => 'pastry'], [
            'name' => 'Pastry',
        ]);

        $products = [];
        $products[] = Product::firstOrCreate(['slug' => 'coca-cola'], [
            'name' => 'Coca Cola',
            'category_id' => $soft->id,
            'description' => 'A refreshing soft drink.',
            'price' => 1.99,
            'stock' => 50,
            'is_available' => true,
        ]);

        $products[] = Product::firstOrCreate(['slug' => 'sprite'], [
            'name' => 'Sprite',
            'category_id' => $soft->id,
            'description' => 'A refreshing lemon-lime soda.',
            'price' => 1.99,
            'stock' => 50,
            'is_available' => true,
        ]);

        $products[] = Product::firstOrCreate(['slug' => 'beef-burger'], [
            'name' => 'Beef Burger',
            'category_id' => $meal->id,
            'description' => 'A delicious beef burger.',
            'price' => 5.99,
            'stock' => 20,
            'is_available' => true,
        ]);

        $products[] = Product::firstOrCreate(['slug' => 'club-sandwich'], [
            'name' => 'Club Sandwich',
            'category_id' => $meal->id,
            'description' => 'Classic club sandwich with turkey and bacon.',
            'price' => 6.99,
            'stock' => 15,
            'is_available' => true,
        ]);

        $products[] = Product::firstOrCreate(['slug' => 'espresso'], [
            'name' => 'Espresso',
            'category_id' => $coffee->id,
            'description' => 'Strong and bold espresso shot.',
            'price' => 2.50,
            'stock' => 100,
            'is_available' => true,
        ]);

        $products[] = Product::firstOrCreate(['slug' => 'cappuccino'], [
            'name' => 'Cappuccino',
            'category_id' => $coffee->id,
            'description' => 'Espresso with steamed milk and thick foam.',
            'price' => 3.50,
            'stock' => 50,
            'is_available' => true,
        ]);

        $products[] = Product::firstOrCreate(['slug' => 'croissant'], [
            'name' => 'Croissant',
            'category_id' => $pastry->id,
            'description' => 'Flaky and buttery french croissant.',
            'price' => 2.99,
            'stock' => 30,
            'is_available' => true,
        ]);

        $products[] = Product::firstOrCreate(['slug' => 'blueberry-muffin'], [
            'name' => 'Blueberry Muffin',
            'category_id' => $pastry->id,
            'description' => 'Freshly baked blueberry muffin.',
            'price' => 2.49,
            'stock' => 25,
            'is_available' => true,
        ]);

        // Add Coupons
        $publicCoupon = Coupons::firstOrCreate(['code' => 'WELCOME10'], [
            'name' => 'Welcome Discount',
            'type' => 'percent',
            'value' => 10,
            'min_spend' => 5.00,
            'usage_limit' => 100,
            'user_limit' => 1,
            'is_active' => true,
            'starts_at' => now(),
            'expires_at' => now()->addDays(30),
        ]);

        // Create 25 orders spread in April 2026
        $allProducts = \App\Models\Product::all();
        for ($i = 0; $i < 25; $i++) {
            $user = $users->random();
            // Random date in April 2026
            $orderDate = \Illuminate\Support\Carbon::create(2026, 4, rand(1, 30), rand(8, 20), rand(0, 59), rand(0, 59));

            $subtotal = 0;
            $orderItems = [];

            $itemsCount = rand(1, 3);
            $selectedProducts = $allProducts->random($itemsCount);

            foreach ($selectedProducts as $product) {
                $qty = rand(1, 2);
                $orderItems[] = [
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'unit_price' => $product->price,
                    'created_at' => $orderDate,
                    'updated_at' => $orderDate,
                ];
                $subtotal += ($product->price * $qty);
            }

            // Using forceCreate to bypass guards for subtotal and total_price
            $order = Order::forceCreate([
                'user_id' => $user->id,
                'subtotal' => $subtotal,
                'discount_amount' => 0,
                'total_price' => $subtotal,
                'status' => 'completed',
                'created_at' => $orderDate,
                'updated_at' => $orderDate,
            ]);

            foreach ($orderItems as $item) {
                $item['order_id'] = $order->id;
                OrderDetails::create($item);
            }
        }
    }
}
