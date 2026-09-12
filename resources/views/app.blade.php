<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script>
        (function () {
            const stored = localStorage.getItem('portfotilo-theme');
            if (!stored) {
                return;
            }
            const dark = stored === 'dark' || (stored === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches);
            document.documentElement.classList.toggle('dark', dark);
        })();
    </script>
    @include('partials.ui-locale-script')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @inertiaHead
</head>
<body class="min-h-screen bg-background font-sans text-foreground antialiased">
    @inertia
    <script>
        (function () {
            if (localStorage.getItem('portfotilo-theme')) {
                return;
            }
            let theme = 'system';
            try {
                const page = JSON.parse(document.querySelector('[data-page]')?.getAttribute('data-page') || '{}');
                theme = page.props?.theme || 'system';
            } catch (e) {}
            const dark = theme === 'dark' || (theme === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches);
            document.documentElement.classList.toggle('dark', dark);
        })();
    </script>
</body>
</html>
