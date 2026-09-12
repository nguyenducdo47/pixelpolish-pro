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
    </style>
</head>
<body>
    <div class="header {{ $layout === 'classic' ? 'classic-header' : '' }}">
        <h1>{{ $profile['full_name'] ?? '' }}</h1>
        <p class="muted">{{ $profile['headline'] ?? '' }}</p>
        <p class="muted">
            {{ $profile['email'] ?? '' }}
            @if(!empty($profile['phone'])) · {{ $profile['phone'] }} @endif
            @if(!empty($profile['location'])) · {{ $profile['location'] }} @endif
        </p>
    </div>

    @include('cv.partials.main', ['portfolio' => $portfolio, 'profile' => $profile, 'settings' => $settings, 'only' => 'about'])
    @include('cv.partials.main', ['portfolio' => $portfolio, 'profile' => $profile, 'settings' => $settings, 'only' => 'education'])
    @include('cv.partials.side', ['portfolio' => $portfolio, 'profile' => $profile, 'settings' => $settings, 'only' => 'skills'])
    @include('cv.partials.main', ['portfolio' => $portfolio, 'profile' => $profile, 'settings' => $settings, 'only' => 'projects'])
    @include('cv.partials.side', ['portfolio' => $portfolio, 'profile' => $profile, 'settings' => $settings, 'only' => 'languages'])
    @include('cv.partials.main', ['portfolio' => $portfolio, 'profile' => $profile, 'settings' => $settings, 'only' => 'principles'])
</body>
</html>
