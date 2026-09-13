<!DOCTYPE html>
<html lang="{{ $portfolio['locale'] ?? app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    @php
        $profile = $portfolio['profile'] ?? [];
        $settings = $portfolio['cv']['settings'] ?? [];
        $appearance = $portfolio['appearance'] ?? [];
        $primary = $appearance['colors']['primary'] ?? '#1a9aa3';
        $muted = $appearance['colors']['muted'] ?? '#52525b';
        $text = $appearance['colors']['foreground'] ?? '#18181b';
        $layout = $appearance['cv_layout'] ?? ($settings['template'] ?? 'modern');

        $github = collect($portfolio['social_links'] ?? [])
            ->first(fn ($link) => str_contains(strtolower($link['platform'] ?? ''), 'git'));

        $stripProtocol = fn ($url) => preg_replace('#^https?://#', '', $url ?? '');

        // Chuyển path SVG thành data URI, để DomPDF render qua <img> thay vì parse <svg> trực tiếp
        $svgIcon = function (string $pathD, bool $filled = false) use ($muted) {
            $attr = $filled
                ? 'fill="'.$muted.'"'
                : 'fill="none" stroke="'.$muted.'" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"';

            $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" '.$attr.'>'.$pathD.'</svg>';

            return 'data:image/svg+xml;base64,'.base64_encode($svg);
        };

        $icons = [
            'calendar' => $svgIcon('<path d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />'),
            'envelope' => $svgIcon('<path d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />'),
            'phone' => $svgIcon('<path d="M2.25 6.75c0 8.284 6.716 15 15 15h1.5a2.25 2.25 0 0 0 2.25-2.25v-1.372a1 1 0 0 0-.628-.929l-4.417-1.767a1 1 0 0 0-1.185.322l-.573.765a2.25 2.25 0 0 1-2.847.598 11.25 11.25 0 0 1-4.5-4.5 2.25 2.25 0 0 1 .598-2.847l.765-.573a1 1 0 0 0 .322-1.185L7.918 3.128a1 1 0 0 0-.928-.628H5.625a2.25 2.25 0 0 0-2.25 2.25v.001z" />'),
            'location' => $svgIcon('<path d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /><path d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />'),
            'website' => $svgIcon('<path d="M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18Z" /><path d="M3.6 9h16.8M3.6 15h16.8M11.25 3a17.25 17.25 0 0 0 0 18M12.75 3a17.25 17.25 0 0 1 0 18" />'),
            'github' => $svgIcon('<path fill-rule="evenodd" clip-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.833.092-.647.35-1.088.636-1.339-2.221-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.269 2.75 1.026A9.564 9.564 0 0 1 12 6.844c.85.004 1.705.115 2.504.337 1.909-1.295 2.747-1.026 2.747-1.026.546 1.378.203 2.397.1 2.65.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.919.678 1.852 0 1.336-.012 2.415-.012 2.743 0 .268.18.58.688.482A10.02 10.02 0 0 0 22 12.017C22 6.484 17.522 2 12 2Z" />', filled: true),
        ];
    @endphp
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: {{ $text }}; line-height: 1.45; }
        h1 { font-size: 22px; margin: 0 0 4px; }
        h2 { font-size: 12px; text-transform: uppercase; letter-spacing: .08em; color: {{ $primary }}; margin: 18px 0 6px; border-bottom: 1px solid {{ $primary }}; padding-bottom: 3px; }
        h3 { font-size: 13px; margin: 0; }
        p, ul { margin: 4px 0; }
        .color { color: var(--color); }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #d4d4d8; padding: 4px 6px; }
        .muted { color: {{ $muted }}; }
        .row { display: table; width: 100%; }
        .left { display: table-cell; }
        .right { display: table-cell; text-align: right; white-space: nowrap; color: {{ $muted }}; }
        .header { border-bottom: 2px solid {{ $primary }}; padding-bottom: 8px; margin-bottom: 8px; }
        .classic-header { text-align: center; }

        .header-row { display: table; width: 100%; table-layout: fixed; }
        .header-left { display: table-cell; vertical-align: top; }
        .header-left-inner { display: table; }
        .header-avatar { display: table-cell; width: 60px; vertical-align: middle; padding-right: 20px; }
        .header-avatar img { border-radius: 50%; object-fit: cover; }
        .header-info { display: table-cell; vertical-align: middle; }
        .header-contact { display: table-cell; width: 180px; vertical-align: top; text-align: right; }

        .contact-list { display: table; margin-top: 4px; }
        .header-contact .contact-list { margin-top: 0; margin-left: auto; }
        .contact-line { display: table-row; }
        .contact-icon { display: table-cell; width: 14px; padding-right: 5px; vertical-align: middle; }
        .contact-icon img { width: 10px; height: 10px; }
        .contact-value { display: table-cell; vertical-align: middle; color: {{ $muted }}; text-align: left; }
    </style>
</head>
<body>
    <div class="header {{ $layout === 'classic' ? 'classic-header' : '' }}">
        <div class="header-row">
            <div class="header-left">
                <div class="header-left-inner">
                    @if(!empty($settings['show_avatar']) && !empty($profile['avatar']))
                        <div class="header-avatar">
                            <img src="{{ $profile['avatar'] }}" width="100" height="100" loading="lazy">
                        </div>
                    @endif

                    <div class="header-info">
                        <h1>{{ $profile['full_name'] ?? '' }}</h1>
                        <p class="muted">{{ $profile['headline'] ?? '' }}</p>
                    </div>
                </div>
            </div>

            <div class="header-contact">
                <div class="contact-list">
                    @if(!empty($profile['date_of_birth']))
                        <div class="contact-line">
                            <div class="contact-icon"><img src="{{ $icons['calendar'] }}" width="10" height="10"></div>
                            <div class="contact-value">{{ $profile['date_of_birth'] }}</div>
                        </div>
                    @endif

                    @if(!empty($profile['email']))
                        <div class="contact-line">
                            <div class="contact-icon"><img src="{{ $icons['envelope'] }}" width="10" height="10"></div>
                            <div class="contact-value">{{ $profile['email'] }}</div>
                        </div>
                    @endif

                    @if(!empty($profile['phone']))
                        <div class="contact-line">
                            <div class="contact-icon"><img src="{{ $icons['phone'] }}" width="10" height="10"></div>
                            <div class="contact-value">{{ $profile['phone'] }}</div>
                        </div>
                    @endif

                    @if(!empty($profile['location']))
                        <div class="contact-line">
                            <div class="contact-icon"><img src="{{ $icons['location'] }}" width="10" height="10"></div>
                            <div class="contact-value">{{ $profile['location'] }}</div>
                        </div>
                    @endif

                    @if(!empty($profile['website']))
                        <div class="contact-line">
                            <div class="contact-icon"><img src="{{ $icons['website'] }}" width="10" height="10"></div>
                            <div class="contact-value">{{ $stripProtocol($profile['website']) }}</div>
                        </div>
                    @endif

                    @if(!empty($github['url']))
                        <div class="contact-line">
                            <div class="contact-icon"><img src="{{ $icons['github'] }}" width="10" height="10"></div>
                            <div class="contact-value">{{ $stripProtocol($github['url']) }}</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @include('cv.partials.main', ['portfolio' => $portfolio, 'profile' => $profile, 'settings' => $settings, 'only' => 'about'])
    @include('cv.partials.main', ['portfolio' => $portfolio, 'profile' => $profile, 'settings' => $settings, 'only' => 'education'])
    @include('cv.partials.side', ['portfolio' => $portfolio, 'profile' => $profile, 'settings' => $settings, 'only' => 'skills'])
    @include('cv.partials.main', ['portfolio' => $portfolio, 'profile' => $profile, 'settings' => $settings, 'only' => 'projects'])
    @include('cv.partials.side', ['portfolio' => $portfolio, 'profile' => $profile, 'settings' => $settings, 'only' => 'languages'])
    @include('cv.partials.main', ['portfolio' => $portfolio, 'profile' => $profile, 'settings' => $settings, 'only' => 'principles'])
</body>
</html>