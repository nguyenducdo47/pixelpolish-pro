@php
    $only = $only ?? null;
    $sidebarSection = $sidebarSection ?? false;
    $classicEntries = $classicEntries ?? false;
    $modernEntries = $modernEntries ?? false;
    $forPdf = $forPdf ?? false;
    $uiLabel = fn (string $dotKey, string $fallbackKey) => data_get($portfolio, 'ui.'.$dotKey) ?? __($fallbackKey);
@endphp

@if(($only === null || $only === 'work_experience') && ($settings['show_work_experience'] ?? true) && !empty($portfolio['work_experiences']))
    @if($sidebarSection)<section class="cv-sidebar-block">@elseif($classicEntries)<section class="cv-classic-section">@elseif($modernEntries)<section class="cv-modern-section">@endif
    <h2 class="{{ $sidebarSection ? 'cv-sidebar-heading' : ($classicEntries ? 'cv-classic-section-title' : ($modernEntries ? 'cv-modern-section-title' : '')) }}">{{ $uiLabel('cv.work_experience', 'ui.cv.work_experience') }}</h2>
    @foreach($portfolio['work_experiences'] as $item)
        @if($sidebarSection)
            <p class="cv-work-company edu-degree"><strong>{{ $item['company'] }}</strong></p>
            <p class="cv-work-position edu-school muted">{{ $item['position'] }}</p>
            <p class="edu-period muted">{{ $item['period'] }}</p>
        @elseif($classicEntries)
            <div class="cv-classic-entry">
                <div class="cv-classic-entry-row">
                    <span class="cv-work-company project-title">{{ $item['company'] }}</span>
                    <span class="cv-classic-date">{{ $item['period'] }}</span>
                </div>
                <p class="cv-work-position cv-classic-subline">{{ $item['position'] }}</p>
            </div>
        @elseif($modernEntries)
            <div class="cv-modern-timeline-item cv-work-entry">
                <div class="project-head">
                    <span class="cv-work-company project-title">{{ $item['company'] }}</span>
                    <span class="project-period">{{ $item['period'] }}</span>
                </div>
                <p class="cv-work-position muted">{{ $item['position'] }}</p>
            </div>
        @else
            <p><strong>{{ $item['company'] }}</strong> — <span class="cv-work-position">{{ $item['position'] }}</span> ({{ $item['period'] }})</p>
        @endif
        @if(!empty($item['details']))
            <div class="rich-content muted cv-sidebar-rich">{!! \App\Support\CvPdfHtml::rich($item['details'], $forPdf) !!}</div>
        @endif
    @endforeach
    @if($sidebarSection || $classicEntries || $modernEntries)</section>@endif
@endif
