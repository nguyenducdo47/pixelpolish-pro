<?php

namespace App\Http\Middleware;

use App\Support\LocaleCatalog;
use App\Support\UiLocale;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = strtolower((string) $request->route('locale'));

        abort_unless(LocaleCatalog::isEnabled($locale), 404);

        UiLocale::remember($locale);

        return $next($request);
    }
}
