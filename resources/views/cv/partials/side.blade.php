@php($only = $only ?? null)

@if(($only === null || $only === 'skills') && ($settings['show_skills'] ?? true) && !empty($portfolio['skill_categories']))
    <h2>{{ __('ui.cv.skills') }}</h2>
    @foreach($portfolio['skill_categories'] as $category)
        <p><strong>{{ $category['name'] }}:</strong> {{ collect($category['skills'])->pluck('name')->join(', ') }}</p>
    @endforeach
@endif

@if(($only === null || $only === 'languages') && ($settings['show_languages'] ?? true) && !empty($portfolio['languages']))
    <h2>{{ __('ui.cv.languages') }}</h2>
    @foreach($portfolio['languages'] as $item)
        <p>{{ $item['name'] }} — {{ $item['level'] }}</p>
    @endforeach
@endif
