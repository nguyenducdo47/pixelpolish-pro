@php($only = $only ?? null)
@php($skillGlue = $skillGlue ?? ', ')
@php($sidebarSection = $sidebarSection ?? false)
@php($classicEntries = $classicEntries ?? false)
@php($modernEntries = $modernEntries ?? false)

@if(($only === null || $only === 'skills') && ($settings['show_skills'] ?? true) && !empty($portfolio['skill_categories']))
    @if($sidebarSection)<section class="cv-sidebar-block">@elseif($classicEntries)<section class="cv-classic-section">@elseif($modernEntries)<section class="cv-modern-section">@endif
    <h2 class="{{ $sidebarSection ? 'cv-sidebar-heading' : ($classicEntries ? 'cv-classic-section-title' : ($modernEntries ? 'cv-modern-section-title' : '')) }}">{{ __('ui.cv.skills') }}</h2>
    @foreach($portfolio['skill_categories'] as $category)
        @if($sidebarSection)
            <p class="sidebar-skill-category"><strong>{{ $category['name'] }}</strong></p>
            <p class="sidebar-skill-list">{{ collect($category['skills'])->pluck('name')->join($skillGlue) }}</p>
        @else
            <p class="{{ $classicEntries || $modernEntries ? 'cv-classic-skill-line' : '' }}"><strong>{{ $category['name'] }}:</strong> {{ collect($category['skills'])->pluck('name')->join($skillGlue) }}</p>
        @endif
    @endforeach
    @if($sidebarSection || $classicEntries || $modernEntries)</section>@endif
@endif

@if(($only === null || $only === 'languages') && ($settings['show_languages'] ?? true) && !empty($portfolio['languages']))
    @if($sidebarSection)<section class="cv-sidebar-block">@elseif($classicEntries)<section class="cv-classic-section">@elseif($modernEntries)<section class="cv-modern-section">@endif
    <h2 class="{{ $sidebarSection ? 'cv-sidebar-heading' : ($classicEntries ? 'cv-classic-section-title' : ($modernEntries ? 'cv-modern-section-title' : '')) }}">{{ __('ui.cv.languages') }}</h2>
    @if($sidebarSection)
        <ul>
            @foreach($portfolio['languages'] as $item)
                <li><strong>{{ $item['name'] }}</strong> — {{ $item['level'] }}</li>
            @endforeach
        </ul>
    @elseif($classicEntries)
        <p class="cv-classic-skill-line">
            @foreach($portfolio['languages'] as $index => $item)
                @if($index > 0) · @endif
                <strong>{{ $item['name'] }}</strong> ({{ $item['level'] }})
            @endforeach
        </p>
    @else
        <ul class="cv-pdf-bullet-list">
            @foreach($portfolio['languages'] as $item)
                <li><strong>{{ $item['name'] }}</strong> — {{ $item['level'] }}</li>
            @endforeach
        </ul>
    @endif
    @if($sidebarSection || $classicEntries || $modernEntries)</section>@endif
@endif
