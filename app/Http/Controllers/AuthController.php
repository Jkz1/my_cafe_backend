<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\AuthService;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }
    public function registerAdmin(RegisterRequest $r)
    {
        $result = $this->authService->registerAdmin($r->validated());
        return response()->json($result, 201);
    }
    public function register(RegisterRequest $r)
    {
        $result = $this->authService->register($r->validated());
        return response()->json($result, 201);
    }

    public function login(LoginRequest $r)
    {
        $result = $this->authService->login($r->validated());
        return response()->json($result);
    }
}
