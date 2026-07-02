<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    // public function login(Request $request)
    // {
    //     $credentials = $request->only('email', 'password');

    //     if (!$token = JWTAuth::attempt($credentials)) {
    //         return response()->json(['error' => 'Unauthorized'], 401);
    //     }

    //     return response()->json([
    //         'token' => $token,
    //         'expiry' => now()->addHours(24)->toDateTimeString(),
    //     ]);
    // }

    public function login(Request $request)
    {
        \Log::info('Login attempt', ['email' => $request->email]);
        
        $credentials = $request->only('email', 'password');
        
        \Log::info('Credentials', $credentials);
        
        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                \Log::warning('Invalid credentials');
                return response()->json(['error' => 'Unauthorized'], 401);
            }
        } catch (\Exception $e) {
            \Log::error('JWT Error: '.$e->getMessage());
            return response()->json(['error' => 'Could not create token'], 500);
        }
        
        \Log::info('Login successful', ['user_id' => auth()->id()]);
        
        return response()->json([
            'token' => $token,
            'expiry' => now()->addHours(24)->toDateTimeString(),
        ]);
    }
}