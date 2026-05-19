<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\ApiKey;
use Symfony\Component\HttpFoundation\Response;

class ApiKeyMiddleware
{
    public function handle(Request $request, Closure $next, string $permission = null): Response
    {
        $apiKey = $request->header('X-API-Key') ?? $request->input('api_key');

        if (!$apiKey) {
            return response()->json([
                'success' => false,
                'message' => 'API key is required'
            ], 401);
        }

        $key = ApiKey::where('key', $apiKey)->first();

        if (!$key) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid API key'
            ], 401);
        }

        if (!$key->isValid()) {
            return response()->json([
                'success' => false,
                'message' => 'API key is expired or inactive'
            ], 401);
        }

        if ($permission && !$key->hasPermission($permission)) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient permissions'
            ], 403);
        }

        // Mark API key as used
        $key->markAsUsed();

        // Attach user to request
        $request->merge(['api_user' => $key->user]);

        return $next($request);
    }
}
