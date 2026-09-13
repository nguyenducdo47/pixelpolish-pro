@php
    $current = app()->getLocale();
    $locales = \App\Support\LocaleCatalog::enabled();
@endphp

@if ($locales->isNotEmpty())
    <div
        x-data="{
            open: false,
            current: '{{ $current }}',
            locales: {{ $locales->map(fn ($l) => ['code' => $l->code, 'label' => $l->native_name.' ('.strtoupper($l->code).')'])->values()->toJson() }},
            select(code) {
                this.current = code;
                this.open = false;
                window.__setUiLocale && window.__setUiLocale(code);
            },
            currentLabel() {
                return this.locales.find(l => l.code === this.current)?.label ?? '';
            },
        }"
        @keydown.escape.window="open = false"
        @click.outside="open = false"
        class="locale-switcher"
    >
        <button
            type="button"
            role="combobox"
            aria-haspopup="listbox"
            :aria-expanded="open"
            aria-label="{{ __('panel.fields.locale') }}"
            @click="open = !open"
            class="locale-switcher__btn"
        >
            <span class="locale-switcher__label" x-text="currentLabel()"></span>

            <svg
                class="locale-switcher__icon"
                :class="{ 'is-open': open }"
                width="20"
                height="20"
                fill="none"
                viewBox="0 0 24 24"
                stroke-width="1.5"
                stroke="currentColor"
                aria-hidden="true"
            >
                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" />
            </svg>
        </button>

        <div
            x-show="open"
            x-transition:enter="locale-switcher__transition-enter"
            x-transition:enter-start="locale-switcher__transition-enter-start"
            x-transition:enter-end="locale-switcher__transition-enter-end"
            x-transition:leave="locale-switcher__transition-leave"
            x-transition:leave-start="locale-switcher__transition-leave-start"
            x-transition:leave-end="locale-switcher__transition-leave-end"
            class="locale-switcher__panel"
            role="listbox"
            style="display: none;"
        >
            <ul class="locale-switcher__list">
                <template x-for="locale in locales" :key="locale.code">
                    <li
                        class="locale-switcher__option"
                        :class="{ 'is-selected': locale.code === current }"
                        role="option"
                        :aria-selected="locale.code === current"
                        tabindex="0"
                        @click="select(locale.code)"
                        @keydown.enter="select(locale.code)"
                    >
                        <span x-text="locale.label"></span>
                    </li>
                </template>
            </ul>
        </div>
    </div>

    <style>
        .locale-switcher {
            position: relative;
            display: inline-block;
        }

        .locale-switcher__btn {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.5rem;
            width: 100%;
            padding: 0.375rem 0.75rem;
            font-size: 0.875rem;
            font-weight: 500;
            color: #374151;
            background-color: #ffffff;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
            cursor: pointer;
            transition: background-color 0.1s ease, border-color 0.1s ease;
        }

        .locale-switcher__btn:hover {
            background-color: #f9fafb;
        }

        .locale-switcher__label {
            white-space: nowrap;
        }

        .locale-switcher__icon {
            flex-shrink: 0;
            color: #9ca3af;
            transition: transform 0.15s ease;
        }

        .locale-switcher__icon.is-open {
            transform: rotate(180deg);
        }

        .locale-switcher__panel {
            position: absolute;
            right: 0;
            z-index: 50;
            margin-top: 0.375rem;
            width: 11rem;
            overflow: hidden;
            background-color: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 0.75rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -4px rgba(0, 0, 0, 0.1);
            padding: 0.25rem 0;
        }

        .locale-switcher__list {
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .locale-switcher__option {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.5rem 0.75rem;
            font-size: 0.875rem;
            color: #374151;
            cursor: pointer;
            transition: background-color 0.075s ease;
        }

        .locale-switcher__option:hover {
            background-color: #f3f4f6;
        }

        .locale-switcher__option.is-selected {
            font-weight: 600;
            color: #2563eb;
            background-color: #eff6ff;
        }

        /* Dark mode: Filament/Tailwind toggle class "dark" on <html> */
        .dark .locale-switcher__btn {
            color: #e5e7eb;
            background-color: rgba(255, 255, 255, 0.05);
            border-color: rgba(255, 255, 255, 0.1);
        }

        .dark .locale-switcher__btn:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        .dark .locale-switcher__icon {
            color: #6b7280;
        }

        .dark .locale-switcher__panel {
            background-color: #1f2937;
            border-color: rgba(255, 255, 255, 0.1);
        }

        .dark .locale-switcher__option {
            color: #e5e7eb;
        }

        .dark .locale-switcher__option:hover {
            background-color: rgba(255, 255, 255, 0.05);
        }

        .dark .locale-switcher__option.is-selected {
            color: #60a5fa;
            background-color: rgba(37, 99, 235, 0.1);
        }

        /* Transition classes (dùng để thay x-transition Tailwind) */
        .locale-switcher__transition-enter {
            transition: opacity 0.1s ease-out, transform 0.1s ease-out;
        }
        .locale-switcher__transition-enter-start {
            opacity: 0;
            transform: scale(0.95);
        }
        .locale-switcher__transition-enter-end {
            opacity: 1;
            transform: scale(1);
        }
        .locale-switcher__transition-leave {
            transition: opacity 0.075s ease-in, transform 0.075s ease-in;
        }
        .locale-switcher__transition-leave-start {
            opacity: 1;
            transform: scale(1);
        }
        .locale-switcher__transition-leave-end {
            opacity: 0;
            transform: scale(0.95);
        }
    </style>
@endif