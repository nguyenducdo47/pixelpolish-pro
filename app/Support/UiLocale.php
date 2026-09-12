<?php

namespace App\Support;

use Illuminate\Http\Request;

class UiLocale
{
    public const SESSION_KEY = 'ui_locale';

    public const STORAGE_KEY = 'portfotilo-locale';

    public static function applyFromRequest(Request $request): string
    {
        $candidates = [
            self::pathLocale($request),
            $request->query('locale'),
            $request->session()->get(self::SESSION_KEY),
            'vi',
        ];

        foreach ($candidates as $code) {
            if (! is_string($code) || ! LocaleCatalog::isEnabled($code)) {
                continue;
            }

            return self::set(strtolower($code));
        }

        return self::set(self::fallback());
    }

    public static function remember(string $code): string
    {
        return self::set($code);
    }

    public static function set(string $code): string
    {
        $code = strtolower($code);

        if (! LocaleCatalog::isEnabled($code)) {
            $code = self::fallback();
        }

        app()->setLocale($code);

        if (session()->isStarted()) {
            session()->put(self::SESSION_KEY, $code);
        }

        return $code;
    }

    public static function current(): string
    {
        $locale = app()->getLocale();

        return LocaleCatalog::isEnabled($locale) ? $locale : self::fallback();
    }

    public static function fallback(): string
    {
        if (LocaleCatalog::isEnabled('vi')) {
            return 'vi';
        }

        return LocaleCatalog::defaultCode();
    }

    public static function pathLocale(Request $request): ?string
    {
        $segment = strtolower((string) $request->segment(1));

        return LocaleCatalog::isEnabled($segment) ? $segment : null;
    }
}
