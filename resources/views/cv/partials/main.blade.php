@php($only = $only ?? null)

@if(($only === null || $only === 'about') && ($settings['show_about'] ?? true) && !empty($profile['about']))
    <h2>{{ __('ui.cv.summary') }}</h2>
    {!! $profile['about'] !!}
@endif

@if(($only === null || $only === 'projects') && ($settings['show_projects'] ?? true) && !empty($portfolio['projects']))
    <h2>{{ __('ui.cv.experience') }}</h2>
    @foreach($portfolio['projects'] as $project)
        <div class="row">
            <h3 class="left">{{ $project['title'] }}</h3>
            <span class="right">{{ $project['period'] }}</span>
        </div>
        {!! $project['solution'] ?: $project['summary'] !!}
        @if(!empty($project['highlights']))
            <ul>
                @foreach($project['highlights'] as $item)
                    <li>{{ $item }}</li>
                @endforeach
            </ul>
        @endif
    @endforeach
@endif

@if(($only === null || $only === 'education') && ($settings['show_education'] ?? true) && !empty($portfolio['education']))
    <h2>{{ __('ui.cv.education') }}</h2>
    @foreach($portfolio['education'] as $item)
        <p><strong>{{ $item['degree'] }}</strong> — {{ $item['school'] }} ({{ $item['period'] }})</p>
        @if(!empty($item['details']))
            {!! $item['details'] !!}
        @endif
    @endforeach
@endif

@if(($only === null || $only === 'principles') && ($settings['show_principles'] ?? true) && !empty($portfolio['principles']))
    <h2>{{ __('ui.philosophy.title') }}</h2>
    @foreach($portfolio['principles'] as $item)
        <p><strong>{{ $item['title'] }}</strong>
            @if(!empty($item['description']))
                — {!! $item['description'] !!}
            @endif
        </p>
    @endforeach
@endif
