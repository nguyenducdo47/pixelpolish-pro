@php
    use App\Filament\Resources\UserInboxMessages\UserInboxMessageResource;
    use Filament\Facades\Filament;

    $user = Filament::auth()->user();
    $count = $user?->unreadInboxCount() ?? 0;
    $url = UserInboxMessageResource::getUrl('index');
@endphp

@if ($user)
    <div class="fi-inbox-bell-ctn inline-flex shrink-0">
        <x-filament::icon-button
        tag="a"
        :href="$url"
        :icon="\Filament\Support\Icons\Heroicon::OutlinedBell"
        color="gray"
        :badge="$count > 0 ? ($count > 99 ? '99+' : (string) $count) : null"
        badge-color="danger"
        :label="__('panel.inbox.title')"
        class="fi-inbox-bell"
    />
    </div>
@endif
