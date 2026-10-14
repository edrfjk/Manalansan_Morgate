<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsCustomer
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            return redirect(url('/auth') . '?as=customer&next=' . urlencode($request->path()));
        }

        // The shop is for customers. An admin has no customers row, no
        // delivery address, and no business placing orders in their own shop.
        if (Auth::user()->role === 'admin') {
            return redirect(url('/avenza/admin'))
                ->with('error', 'Admin accounts cannot shop. Use a customer account to place orders.');
        }

        return $next($request);
    }
}