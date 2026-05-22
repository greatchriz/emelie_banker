<?php

namespace App\Http\Middleware;

use App\Models\Generalsetting;
use Closure;

class Otp
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $gs = Generalsetting::first();

        $user = auth()->user();

        if (!$user) {
            return $next($request);
        }

        if ($gs && $gs->two_factor && $user->twofa) {
            if ($user->verified == 0) {
                return redirect()->route('user.otp');
            }
        }

        return $next($request);
    }
}