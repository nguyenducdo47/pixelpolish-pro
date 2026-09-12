<x-filament-widgets::widget class="fi-getting-started-widget">
    <style>
        .portfolio-guide {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .portfolio-guide-summary {
            font-size: 0.95rem;
            font-weight: 650;
            color: var(--gray-950);
        }

        .dark .portfolio-guide-summary {
            color: #fff;
        }

        .portfolio-guide .fi-sc-wizard-header {
            display: grid !important;
            grid-template-columns: repeat(auto-fill, minmax(10.5rem, 1fr));
            gap: 0.5rem;
            overflow: visible !important;
            padding: 0;
        }

        .portfolio-guide .fi-sc-wizard-header-step {
            width: 100%;
            min-width: 0;
            max-width: none;
            position: relative;
        }

        .portfolio-guide .fi-sc-wizard-header-step-separator {
            display: none !important;
        }

        .portfolio-guide .fi-sc-wizard-header-step-btn {
            width: 100%;
            column-gap: 0.5rem !important;
            padding: 0.55rem 0.75rem !important;
            border-radius: 0.75rem;
            border: 0;
            text-align: left;
            cursor: pointer;
        }

        .portfolio-guide .fi-sc-wizard-header-step.is-todo .fi-sc-wizard-header-step-btn {
            background: #fee2e2;
            color: #991b1b;
            box-shadow: inset 0 0 0 1px #f87171;
        }

        .portfolio-guide .fi-sc-wizard-header-step.is-done .fi-sc-wizard-header-step-btn {
            background: #dcfce7;
            color: #166534;
            box-shadow: inset 0 0 0 1px #4ade80;
        }

        .portfolio-guide .fi-sc-wizard-header-step.fi-active .fi-sc-wizard-header-step-btn {
            box-shadow: inset 0 0 0 2px currentColor;
        }

        .portfolio-guide .fi-sc-wizard-header-step-icon-ctn {
            width: 1.75rem !important;
            height: 1.75rem !important;
            border-radius: 999px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .portfolio-guide .fi-sc-wizard-header-step.is-todo .fi-sc-wizard-header-step-icon-ctn {
            background: #dc2626;
            color: #fff;
        }

        .portfolio-guide .fi-sc-wizard-header-step.is-done .fi-sc-wizard-header-step-icon-ctn {
            background: #16a34a;
            color: #fff;
        }

        .portfolio-guide .fi-sc-wizard-header-step-icon-ctn .fi-icon {
            width: 1rem;
            height: 1rem;
        }

        .portfolio-guide .fi-sc-wizard-header-step-text {
            width: auto !important;
            max-width: 100% !important;
        }

        .portfolio-guide .fi-sc-wizard-header-step-label {
            font-weight: 650;
        }

        .portfolio-guide .fi-sc-wizard-header-step-description {
            display: block;
            font-size: 0.75rem;
            opacity: 0.8;
        }

        .dark .portfolio-guide .fi-sc-wizard-header-step.is-todo .fi-sc-wizard-header-step-btn {
            background: rgb(239 68 68 / 0.2);
            color: #fca5a5;
            box-shadow: inset 0 0 0 1px rgb(248 113 113 / 0.55);
        }

        .dark .portfolio-guide .fi-sc-wizard-header-step.is-done .fi-sc-wizard-header-step-btn {
            background: rgb(34 197 94 / 0.2);
            color: #86efac;
            box-shadow: inset 0 0 0 1px rgb(74 222 128 / 0.55);
        }

        .portfolio-guide-panel {
            border-radius: 0.75rem;
            border: 1px solid var(--gray-200);
            padding: 1rem;
        }

        .dark .portfolio-guide-panel {
            border-color: rgb(255 255 255 / 0.12);
        }

        .portfolio-guide-panel-title {
            margin: 0;
            font-size: 1rem;
            font-weight: 800;
            color: var(--gray-950);
        }

        .dark .portfolio-guide-panel-title {
            color: #fff;
        }

        .portfolio-guide-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
            padding: 0.4rem 0;
            font-size: 0.9rem;
            font-weight: 600;
        }

        .portfolio-guide-item.is-done {
            color: #16a34a;
        }

        .portfolio-guide-item.is-todo,
        .portfolio-guide-item.is-todo a {
            color: #dc2626;
        }

        .dark .portfolio-guide-item.is-done {
            color: #4ade80;
        }

        .dark .portfolio-guide-item.is-todo,
        .dark .portfolio-guide-item.is-todo a {
            color: #f87171;
        }

        .portfolio-guide-item a {
            text-decoration: underline;
            text-underline-offset: 3px;
        }
    </style>

    <x-filament::section>
        <x-slot name="heading">
            {{ __('panel.guide.title') }}
        </x-slot>

        <x-slot name="afterHeader">
            <div class="flex flex-wrap gap-2">
                <x-filament::button tag="a" color="gray" :href="$previewSite" target="_blank">
                    {{ __('panel.guide.preview_site') }}
                </x-filament::button>
                <x-filament::button tag="a" color="gray" :href="$previewCv" target="_blank">
                    {{ __('panel.guide.preview_cv') }}
                </x-filament::button>
                <x-filament::button tag="a" :href="$wizardUrl">
                    {{ __('panel.guide.start_wizard') }}
                </x-filament::button>
            </div>
        </x-slot>

        <div class="portfolio-guide">
            <p class="portfolio-guide-summary">
                {{ __('panel.guide.summary', [
                    'steps_done' => $steps_done,
                    'steps_total' => $steps_total,
                    'items_pending' => $items_pending,
                    'items_total' => $items_total,
                ]) }}
            </p>

            <ol class="fi-sc-wizard-header" role="list">
                @foreach ($steps as $step)
                    <li
                        @class([
                            'fi-sc-wizard-header-step',
                            'is-done' => $step['done'],
                            'is-todo' => ! $step['done'],
                            'fi-active' => $active['key'] === $step['key'],
                        ])
                    >
                        <button
                            type="button"
                            class="fi-sc-wizard-header-step-btn"
                            wire:click="selectStep('{{ $step['key'] }}')"
                        >
                            <div class="fi-sc-wizard-header-step-icon-ctn">
                                @if ($step['done'])
                                    <x-filament::icon icon="heroicon-m-check" />
                                @else
                                    <x-filament::icon icon="heroicon-m-x-mark" />
                                @endif
                            </div>
                            <div class="fi-sc-wizard-header-step-text">
                                <span class="fi-sc-wizard-header-step-label">{{ $step['title'] }}</span>
                                <span class="fi-sc-wizard-header-step-description">
                                    {{ __('panel.guide.step_items', [
                                        'done' => collect($step['items'])->where('done', true)->count(),
                                        'total' => count($step['items']),
                                    ]) }}
                                </span>
                            </div>
                        </button>
                    </li>
                @endforeach
            </ol>

            <div class="portfolio-guide-panel">
                <div class="mb-2 flex flex-wrap items-center justify-between gap-3">
                    <h3 class="portfolio-guide-panel-title">{{ $active['title'] }}</h3>

                    @unless ($active['done'])
                        <a href="{{ $active['url'] }}" class="portfolio-guide-item is-todo" style="padding: 0">
                            {{ __('panel.guide.go_to_step') }}
                        </a>
                    @endunless
                </div>

                <ul>
                    @foreach ($active['items'] as $item)
                        <li @class(['portfolio-guide-item', 'is-done' => $item['done'], 'is-todo' => ! $item['done']])>
                            @if ($item['done'])
                                <span>{{ $item['label'] }}</span>
                            @else
                                <a href="{{ $item['url'] }}">{{ $item['label'] }}</a>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
