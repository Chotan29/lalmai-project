<?php

// app/Http/Middleware/VerifyApiToken.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;

class VerifyApiToken
{
    public function handle($request, Closure $next)
    {
        $token = $request->bearerToken() ?? $request->input('api_token');

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'API token required',
                'payload' => null
            ], 401);
        }

        $apiToken = DB::table('api_tokens')
            ->where('token', hash('sha256', $token))
            ->where(function($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->first();

        if (!$apiToken) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid API token',
                'payload' => null
            ], 401);
        }

        return $next($request);
    }
}