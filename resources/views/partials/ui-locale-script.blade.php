<script>
    window.__PORTFOTILO_LOCALES = @json(\App\Support\LocaleCatalog::codes());
    window.__PORTFOTILO_LOCALE = @json(\App\Support\UiLocale::current());
    window.__PORTFOTILO_LOCALE_KEY = @json(\App\Support\UiLocale::STORAGE_KEY);

    (function () {
        var key = window.__PORTFOTILO_LOCALE_KEY;
        var codes = window.__PORTFOTILO_LOCALES || [];
        var params = new URLSearchParams(window.location.search);
        var query = (params.get('locale') || '').toLowerCase();
        var stored = (localStorage.getItem(key) || '').toLowerCase();
        var current = (document.documentElement.lang || window.__PORTFOTILO_LOCALE || 'vi').toLowerCase();

        function isEnabled(code) {
            return code && codes.indexOf(code) !== -1;
        }

        function pathLocale() {
            var match = window.location.pathname.match(/^\/([a-z]{2}(?:-[a-z]{2})?)(?:\/|$)/i);

            if (! match) {
                return null;
            }

            var code = match[1].toLowerCase();

            return isEnabled(code) ? code : null;
        }

        window.__setUiLocale = function (code) {
            code = String(code || '').toLowerCase();

            if (! isEnabled(code)) {
                return;
            }

            localStorage.setItem(key, code);

            var href = window.__uiLocaleHref(code);

            if (href !== window.location.href) {
                window.location.assign(href);
            }
        };

        window.__uiLocaleHref = function (code) {
            var selectedPath = pathLocale();

            if (selectedPath) {
                return window.location.pathname.replace(/^\/[a-z]{2}(?:-[a-z]{2})?/i, '/' + code) + window.location.search + window.location.hash;
            }

            var url = new URL(window.location.href);
            url.searchParams.set('locale', code);

            return url.toString();
        };

        if (isEnabled(query)) {
            localStorage.setItem(key, query);
            return;
        }

        var fromPath = pathLocale();

        if (fromPath) {
            localStorage.setItem(key, fromPath);
            return;
        }

        if (isEnabled(stored) && stored !== current) {
            params.set('locale', stored);
            var next = window.location.pathname + '?' + params.toString() + window.location.hash;
            window.location.replace(next);
        }
    })();
</script>
