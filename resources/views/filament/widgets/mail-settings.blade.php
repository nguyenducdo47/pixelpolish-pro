<x-filament-widgets::widget class="fi-mail-settings-widget">
    <x-filament::section>
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-base font-semibold">
                    {{ __('panel.pages.mail') }}
                </h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ __('panel.fields.mail_mailer_helper') }}
                </p>
            </div>

            <x-filament::button
                color="primary"
                tag="a"
                :href="$url"
            >
                {{ __('panel.nav.mail') }}
            </x-filament::button>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
