<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckApproved
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() || ! $request->user()->is_approved) {
            return response()->json(['message' => 'Your account is not approved yet.'], 403);
        }

        return $next($request);
    }
}
