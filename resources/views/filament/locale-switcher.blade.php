@php
    $current = app()->getLocale();
    $locales = \App\Support\LocaleCatalog::enabled();
@endphp

@if ($locales->isNotEmpty())
    <label class="inline-flex items-center px-2">
        <select
            aria-label="{{ __('panel.fields.locale') }}"
            class="rounded-lg border-none bg-transparent py-1 pl-2 pr-8 text-sm"
            onchange="window.__setUiLocale && window.__setUiLocale(this.value)"
        >
            @foreach ($locales as $locale)
                <option value="{{ $locale->code }}" @selected($current === $locale->code)>
                    {{ $locale->native_name }} ({{ strtoupper($locale->code) }})
                </option>
            @endforeach
        </select>
    </label>
@endif
