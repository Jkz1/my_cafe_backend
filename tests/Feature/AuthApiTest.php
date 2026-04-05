<?php

namespace Tests\Feature;

use App\Models\User;
use Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthApiTest extends TestCase
{

    use RefreshDatabase;
    public function test_login_user(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password123'),
        ]);

        // 2. Perform the POST login
        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);
        $response->assertStatus(200);
    }

    public function test_user_can_register()
    {
        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password',
            'password_confirmation' => 'password'
        ];

        $response = $this->postJson('/api/register', $data);

        // 1. Check response
        $response->assertStatus(201);

        // 2. Check database
        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com'
        ]);
    }
    public function test_cannot_register_with_existing_email()
    {
        // Create existing user
        User::factory()->create([
            'email' => 'john@example.com'
        ]);

        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password',
            'password_confirmation' => 'password'
        ];

        $response = $this->postJson('/api/register', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }
    public function test_register_validation()
    {
        $response = $this->postJson('/api/register', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'name',
                'email',
                'password'
            ]);
    }

    public function test_password_is_hashed()
    {
        $this->postJson('/api/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password',
            'password_confirmation' => 'password'
        ]);

        $user = User::first();

        $this->assertTrue(Hash::check('password', $user->password));
    }

}
