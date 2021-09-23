<?php

namespace App\Http\Middleware;

use App\Models\AccessKeys;
use Closure;
use Illuminate\Http\Request;
use Wikimedia\IPSet;

class AdminAuthMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $accessKey = AccessKeys::query()->where('token', $request->bearerToken())->first();

        if (empty($accessKey)) {
            return response()->json([
                'error' => [
                    'type' => 'xInvalidToken',
                    'info' => 'Invalid token was specified or do not have permission.'
                ]
            ], 403);
        }

        $clientIPRange = $this->checkIPRange($request->getClientIp(), $accessKey->whitelist_range);

        if ($clientIPRange === FALSE) {
            return response()->json([
                'error' => [
                    'type' => 'xInvalidToken',
                    'info' => 'Invalid token was specified or do not have permission.'
                ]
            ], 403);
        }

        return $next($request);
    }

    protected function checkIPRange($ip, $range)
    {
        if (empty($range)) {
            return true;
        }

        $ipSet = new IPSet($range);

        return $ipSet->match($ip);
    }
}
