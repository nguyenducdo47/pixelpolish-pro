<?php

namespace App\Http\Middleware;

use App\Support\LocaleCatalog;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function handle(Request $request, \Closure $next)
    {
        if ($request->is('admin', 'admin/*', 'studio', 'studio/*', 'livewire/*')) {
            return $next($request);
        }

        return parent::handle($request, $next);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'locale' => fn (): string => app()->getLocale(),
            'locales' => fn () => LocaleCatalog::enabled()->map(fn ($item) => [
                'code' => $item->code,
                'name' => $item->name,
                'native_name' => $item->native_name,
            ])->values(),
            'ui' => function (): array {
                $locale = app()->getLocale();
                $uiLocale = is_file(lang_path($locale.'/ui.php'))
                    ? $locale
                    : LocaleCatalog::defaultCode();

                return trans('ui', [], $uiLocale);
            },
            'theme' => 'system',
            'status' => $request->session()->get('status'),
            'auth' => fn (): array => [
                'user' => $request->user() ? [
                    'name' => $request->user()->name,
                    'username' => $request->user()->username,
                    'email' => $request->user()->email,
                    'is_admin' => $request->user()->isAdmin(),
                ] : null,
            ],
        ];
    }
}
