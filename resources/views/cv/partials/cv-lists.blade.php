@php
    $sections = [
        'certifications' => [
            'key' => 'certifications',
            'setting' => 'show_certifications',
            'label' => data_get($portfolio, 'ui.cv.certifications') ?? __('ui.cv.certifications'),
        ],
        'career_targets' => [
            'key' => 'career_targets',
            'setting' => 'show_career_targets',
            'label' => data_get($portfolio, 'ui.cv.career_targets') ?? __('ui.cv.career_targets'),
        ],
        'strengths' => [
            'key' => 'strengths',
            'setting' => 'show_strengths',
            'label' => data_get($portfolio, 'ui.cv.strengths') ?? __('ui.cv.strengths'),
        ],
    ];
    $only = $only ?? null;
    $mainSection = $mainSection ?? false;
    $classicEntries = $classicEntries ?? false;
    $sidebarSection = $sidebarSection ?? false;
    $modernEntries = $modernEntries ?? false;
@endphp

@foreach($sections as $section)
    @if($only !== null && $only !== $section['key'])
        @continue
    @endif
    @php($items = $portfolio[$section['key']] ?? [])
    @if(($settings[$section['setting']] ?? true) && !empty($items))
        @if($mainSection)
            <section class="cv-main-block">
        @elseif($classicEntries)
            <section class="cv-classic-section">
        @elseif($sidebarSection)
            <section class="cv-sidebar-block">
        @elseif($modernEntries)
            <section class="cv-modern-section">
        @endif
        <h2 class="{{ $mainSection ? 'cv-main-heading' : ($classicEntries ? 'cv-classic-section-title' : ($sidebarSection ? 'cv-sidebar-heading' : ($modernEntries ? 'cv-modern-section-title' : ''))) }}">{{ $section['label'] }}</h2>
        <ul class="{{ $classicEntries ? 'cv-classic-list' : ($sidebarSection ? '' : 'cv-pdf-bullet-list') }}">
            @foreach($items as $item)
                <li>{{ $item['title'] ?? '' }}</li>
            @endforeach
        </ul>
        @if($mainSection || $classicEntries || $sidebarSection || $modernEntries)
            </section>
        @endif
    @endif
@endforeach
