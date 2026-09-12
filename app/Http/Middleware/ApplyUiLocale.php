<?php

namespace App\Http\Middleware;

use App\Support\UiLocale;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApplyUiLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        UiLocale::applyFromRequest($request);

        return $next($request);
    }
}
