<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class PartnerAuth
{
    public function handle($request, Closure $next)
    {

        if (!Auth::check()) {
			return redirect('/user/login');
		}

        return $next($request);
    }
}

