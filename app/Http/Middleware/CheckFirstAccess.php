<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckFirstAccess
{
    public function handle($request, Closure $next)
    {
        return $next($request);
    }
}
