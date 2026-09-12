@php
    $page = \Livewire\Livewire::current();
    $urls = $page instanceof \App\Filament\Pages\SetupWizard ? $page->previewUrls() : [];

    $previews = array_values(array_filter([
        filled($urls['site'] ?? null) ? [
            'id' => 'setup-preview-site',
            'url' => $urls['site'],
            'title' => __('panel.wizard.preview_site'),
            'help' => __('panel.wizard.preview_site_help'),
            'open' => __('panel.wizard.open_site'),
            'icon' => \Filament\Support\Icons\Heroicon::OutlinedGlobeAlt,
        ] : null,
        filled($urls['cv'] ?? null) ? [
            'id' => 'setup-preview-cv',
            'url' => $urls['cv'],
            'title' => __('panel.wizard.preview_cv'),
            'help' => __('panel.wizard.preview_cv_help'),
            'open' => __('panel.wizard.open_cv'),
            'icon' => \Filament\Support\Icons\Heroicon::OutlinedDocumentText,
        ] : null,
    ]));
@endphp

<style>
    .setup-preview-modal.fi-modal .fi-modal-window {
        display: flex;
        width: 100vw;
        max-width: 100vw;
        height: 100dvh;
        max-height: 100dvh;
        flex-direction: column;
        border-radius: 0;
    }

    .setup-preview-modal.fi-modal .fi-modal-content {
        display: flex;
        min-height: 0;
        flex: 1 1 auto;
        padding: 0;
    }

    .setup-preview-modal.fi-modal iframe {
        flex: 1 1 auto;
        width: 100%;
        height: 100%;
        min-height: 0;
        border: 0;
        border-radius: 0;
        background: #fff;
    }
</style>

<div class="grid gap-4 sm:grid-cols-2">
    @foreach ($previews as $preview)
        <x-filament::modal
            :id="$preview['id']"
            width="screen"
            :heading="$preview['title']"
            sticky-header
            sticky-footer
            class="setup-preview-modal"
        >
            <x-slot name="trigger">
                <div class="flex w-full cursor-pointer items-center gap-4 rounded-xl border border-gray-200 bg-white p-4 text-start transition hover:border-primary-500 hover:bg-gray-50 dark:border-white/10 dark:bg-white/5 dark:hover:bg-white/10">
                    <span class="flex size-11 shrink-0 items-center justify-center rounded-lg bg-primary-50 text-primary-600 dark:bg-primary-400/10 dark:text-primary-300">
                        <x-filament::icon :icon="$preview['icon']" class="size-6" />
                    </span>
                    <span class="min-w-0 flex-1">
                        <span class="block text-sm font-semibold text-gray-950 dark:text-white">
                            {{ $preview['title'] }}
                        </span>
                        <span class="mt-1 block text-sm text-gray-500 dark:text-gray-400">
                            {{ $preview['help'] }}
                        </span>
                    </span>
                    <x-filament::badge color="primary">
                        {{ __('panel.wizard.preview_click') }}
                    </x-filament::badge>
                </div>
            </x-slot>

            <iframe
                x-bind:src="isOpen ? @js($preview['url']) : ''"
                title="{{ $preview['title'] }}"
            ></iframe>

            <x-slot name="footer">
                <x-filament::button
                    tag="a"
                    :href="$preview['url']"
                    target="_blank"
                    color="gray"
                    size="sm"
                >
                    {{ $preview['open'] }}
                </x-filament::button>
            </x-slot>
        </x-filament::modal>
    @endforeach
</div>
