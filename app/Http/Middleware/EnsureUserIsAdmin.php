<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            return redirect(url('/auth') . '?as=admin&next=' . urlencode($request->path()));
        }

        if (Auth::user()->role !== 'admin') {
            abort(403, 'This area is for shop administrators only.');
        }

        return $next($request);
    }
}