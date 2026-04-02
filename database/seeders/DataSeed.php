<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DataSeed extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'User',
            'email' => 'User@gmail.com',
            'password' => bcrypt('password123'),
        ]);
        User::factory()->create([
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

        $cocaCola = Product::factory()->create([
            'name' => 'Coca Cola',
            'slug' => 'coca-cola',
            'category_id' => $soft->id,
            'description' => 'A refreshing soft drink.',
            'price' => 1.99,
            'stock' => 10,
            'is_available' => true,
        ]);
        $burger = Product::factory()->create([
            'name' => 'Burger',
            'slug' => 'burger',
            'category_id' => $meal->id,
            'description' => 'A delicious beef burger.',
            'price' => 5.99,
            'stock' => 5,
            'is_available' => true,
        ]);
    }
}
