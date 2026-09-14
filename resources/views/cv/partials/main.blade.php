@php($only = $only ?? null)
@php($detailedEducation = $detailedEducation ?? false)
@php($richProjects = $richProjects ?? false)
@php($showTechStack = $showTechStack ?? true)
@php($uiLabel = fn (string $dotKey, string $fallbackKey) => data_get($portfolio, 'ui.'.$dotKey) ?? __($fallbackKey))
@php($mainSection = $mainSection ?? false)
@php($sidebarSection = $sidebarSection ?? false)
@php($classicEntries = $classicEntries ?? false)
@php($modernTimeline = $modernTimeline ?? false)
@php($forPdf = $forPdf ?? false)

@if(($only === null || $only === 'about') && ($settings['show_about'] ?? true) && !empty($profile['about']))
    @if($mainSection)<section class="cv-main-block">@elseif($classicEntries)<section class="cv-classic-section">@endif
    <h2 class="{{ $mainSection ? 'cv-main-heading' : ($classicEntries ? 'cv-classic-section-title' : '') }}">{{ $uiLabel('cv.summary', 'ui.cv.summary') }}</h2>
    <div class="rich-content muted">{!! \App\Support\CvPdfHtml::rich($profile['about'], $forPdf) !!}</div>
    @if($mainSection || $classicEntries)</section>@endif
@endif

@if(($only === null || $only === 'projects') && ($settings['show_projects'] ?? true) && !empty($portfolio['projects']))
    @if($mainSection)<section class="cv-main-block">@endif
    <h2 class="{{ $mainSection ? 'cv-main-heading' : ($classicEntries ? 'cv-classic-section-title' : '') }}">{{ $uiLabel('cv.experience', 'ui.cv.experience') }}</h2>
    @if($modernTimeline)
        <div class="cv-modern-timeline">
    @endif
    @foreach($portfolio['projects'] as $index => $project)
        @php($itemClass = $richProjects && $mainSection ? 'cv-experience-item' : ($classicEntries ? 'cv-classic-entry'.($index > 0 ? ' cv-classic-entry-divider' : '') : 'cv-project'.($modernTimeline ? ' cv-modern-timeline-item' : '')))
        <div class="{{ $itemClass }}">
            <div class="project-head{{ $classicEntries ? ' cv-classic-entry-row' : '' }}">
                @if($richProjects && $mainSection)
                    <h3 class="project-title">{{ $project['title'] }}</h3>
                    <span class="project-period">{{ $project['period'] }}</span>
                @else
                    <span class="project-title">{{ $project['title'] }}</span>
                    <span class="project-period{{ $classicEntries ? ' cv-classic-date' : '' }}">{{ $project['period'] }}</span>
                @endif
            </div>
            @if($richProjects && !empty($project['subtitle']))
                <p class="muted project-subtitle{{ $classicEntries ? ' cv-classic-subline' : '' }}">{{ $project['subtitle'] }}</p>
            @endif
            @php($body = $richProjects ? ($project['summary'] ?? $project['solution'] ?? null) : ($project['solution'] ?: $project['summary']))
            @if(filled($body))
                <div class="rich-content muted">{!! \App\Support\CvPdfHtml::rich($body, $forPdf) !!}</div>
            @endif
            @if(!empty($project['highlights']))
                <ul class="{{ $classicEntries ? 'cv-classic-list' : '' }}">
                    @foreach($project['highlights'] as $item)
                        <li>{{ $item }}</li>
                    @endforeach
                </ul>
            @endif
            @if($richProjects && $showTechStack && !empty($project['tech_stack']))
                <p class="tech-tags">
                    @foreach($project['tech_stack'] as $tech)
                        <span class="tech-tag">{{ $tech }}</span>
                    @endforeach
                </p>
            @endif
        </div>
    @endforeach
    @if($modernTimeline)
        </div>
    @endif
    @if($mainSection)</section>@endif
@endif

@if(($only === null || $only === 'education') && ($settings['show_education'] ?? true) && !empty($portfolio['education']))
    @if($sidebarSection)<section class="cv-sidebar-block">@elseif($classicEntries)<section class="cv-classic-section">@endif
    <h2 class="{{ $sidebarSection ? 'cv-sidebar-heading' : ($classicEntries ? 'cv-classic-section-title' : '') }}">{{ $uiLabel('cv.education', 'ui.cv.education') }}</h2>
    @foreach($portfolio['education'] as $item)
        @if($detailedEducation)
            <p class="edu-degree"><strong>{{ $item['degree'] }}</strong></p>
            <p class="edu-school muted">{{ $item['school'] }}</p>
            <p class="edu-period muted">{{ $item['period'] }}</p>
        @elseif($classicEntries)
            <div class="cv-classic-entry">
                <div class="cv-classic-entry-row">
                    <span class="project-title">{{ $item['degree'] }}</span>
                    <span class="cv-classic-date">{{ $item['period'] }}</span>
                </div>
                <p class="cv-classic-subline">{{ $item['school'] }}</p>
            </div>
        @else
            <p><strong>{{ $item['degree'] }}</strong> — {{ $item['school'] }} ({{ $item['period'] }})</p>
        @endif
        @if(!empty($item['details']))
            <div class="cv-sidebar-rich rich-content">{!! \App\Support\CvPdfHtml::rich($item['details'], $forPdf) !!}</div>
        @endif
    @endforeach
    @if($sidebarSection || $classicEntries)</section>@endif
@endif

@if(($only === null || $only === 'principles') && ($settings['show_principles'] ?? true) && !empty($portfolio['principles']))
    @if($sidebarSection)<section class="cv-sidebar-block">@elseif($classicEntries)<section class="cv-classic-section">@endif
    <h2 class="{{ $sidebarSection ? 'cv-sidebar-heading' : ($classicEntries ? 'cv-classic-section-title' : '') }}">{{ __('ui.philosophy.title') }}</h2>
    @foreach($portfolio['principles'] as $item)
        @if($sidebarSection)
            <p class="sidebar-principle">
                <strong>{{ $item['title'] }}</strong>
                @if(!empty($item['description']))
                    <span class="cv-sidebar-rich rich-content">{!! \App\Support\CvPdfHtml::rich($item['description'], $forPdf) !!}</span>
                @endif
            </p>
        @elseif($classicEntries)
            <div class="cv-classic-entry">
                <p><strong>{{ $item['title'] }}</strong></p>
                @if(!empty($item['description']))
                    <div class="rich-content cv-classic-subline">{!! \App\Support\CvPdfHtml::rich($item['description'], $forPdf) !!}</div>
                @endif
            </div>
        @else
            <p><strong>{{ $item['title'] }}</strong>
                @if(!empty($item['description']))
                    — {!! \App\Support\CvPdfHtml::rich($item['description'], $forPdf) !!}
                @endif
            </p>
        @endif
    @endforeach
    @if($sidebarSection || $classicEntries)</section>@endif
@endif
