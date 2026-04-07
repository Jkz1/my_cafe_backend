<?php

namespace Database\Factories;

use App\Models\Coupons;
use Illuminate\Database\Eloquent\Factories\Factory;
use Str;

/**
 * @extends Factory<Coupons>
 */
class CouponsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = $this->faker->randomElement(['fixed', 'percent']);
        return [
            'name' => $this->faker->words(2, true) . ' Discount',
            'code' => strtoupper(Str::random(8)),
            'type' => $type,
            'value' => $type === 'percent'
                ? $this->faker->randomFloat(2, 5, 50)
                : $this->faker->randomFloat(2, 10, 200),
            'min_spend' => $this->faker->optional(0.7)->randomFloat(2, 0, 100), 
            'usage_limit' => $this->faker->optional(0.5)->numberBetween(50, 500),
            'used_count' => 0, 
            'user_limit' => $this->faker->optional(0.3)->numberBetween(1, 5),
            'is_active' => true,
            'starts_at' => now()->subDay(),
            'expires_at' => now()->addDays($this->faker->numberBetween(7, 30)),
        ];
    }
}
