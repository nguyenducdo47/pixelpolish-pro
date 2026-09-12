<div class="fi-impersonate-banner flex items-center justify-center gap-3 bg-amber-500 px-4 py-2 text-sm font-medium text-amber-950">
    {{ __('panel.impersonate.banner') }}
    <strong>{{ auth()->user()->name }}</strong>
    ({{ auth()->user()->username }})
    —
    <a href="{{ route('impersonation.leave') }}" class="underline">
        {{ __('panel.impersonate.back') }}
    </a>
</div>
