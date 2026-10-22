@php
    $title = $portfolio['cv']['resume_summary_title'] ?? null;
    $summary = $portfolio['cv']['resume_summary'] ?? null;
    $enabled = (bool) ($portfolio['cv']['settings']['show_resume_summary'] ?? true);
    $show = $enabled && (filled($title) || filled($summary));
@endphp
@if($show)
    <div class="cv-resume-summary{{ filled($summary) ? '' : ' cv-resume-summary--title-only' }}">
        <div class="cv-resume-summary-label">{{ $title ?: (data_get($portfolio, 'ui.cv.resume_summary') ?? __('ui.cv.resume_summary')) }}</div>
        @if(filled($summary))
            <div class="cv-resume-summary-body">{{ $summary }}</div>
        @endif
    </div>
@endif
