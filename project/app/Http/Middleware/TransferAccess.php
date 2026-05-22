<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class TransferAccess
{
    public function handle($request, Closure $next)
    {
        $user = $request->user();

        if (!$user && Auth::guard('api')->check()) {
            $user = Auth::guard('api')->user();
        }

        if ($user && !$user->transfer_access) {
            $message = 'Transfer access disabled.';

            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'status' => false,
                    'data' => [],
                    'error' => ['message' => $message],
                ], 403);
            }

            return redirect()->route('user.dashboard')->with('warning', $message);
        }

        return $next($request);
    }
}
