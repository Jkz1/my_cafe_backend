<?php
namespace App\Services;

use App\Models\User;
use Auth;
use Illuminate\Validation\ValidationException;
use Hash;

class AuthService
{
    public function registerAdmin($data) {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
        $user->assignRole('admin');
        $token = $user->createToken('auth_token')->plainTextToken;
        return ['access_token' => $token, 'token_type' => 'Bearer'];
    }
    public function register($data) {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
        $token = $user->createToken('auth_token')->plainTextToken;
        return ['access_token' => $token, 'token_type' => 'Bearer'];
    }
    public function login(array $credentials): array
    {
        if (!Auth::attempt($credentials)) {
            throw ValidationException::withMessages([
                'email' => ['Invalid login details'],
            ]);
        }

        $user = User::where('email', $credentials['email'])->firstOrFail();

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'access_token' => $token,
            'token_type' => 'Bearer'
        ];
    }
}