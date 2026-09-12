<form method="POST" action="{{ route('cache.clear') }}" class="fi-clear-cache">
    @csrf

    <x-filament::button
        type="submit"
        color="gray"
        size="sm"
        icon="heroicon-o-arrow-path"
    >
        {{ __('panel.actions.clear_cache') }}
    </x-filament::button>
</form>
