<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue';

const STORAGE_KEY = 'portfotilo-locale';

const props = defineProps({
    modelValue: {
        type: String,
        default: 'vi',
    },
    options: {
        type: Array,
        default: () => [],
    },
});

const open = ref(false);
const rootEl = ref(null);

function labelFor(item) {
    return item.native_name || item.label || String(item.code || '').toUpperCase();
}

const currentLabel = computed(() => {
    const selected = props.options.find((item) => item.code === props.modelValue);
    return selected ? labelFor(selected) : '';
});

function selectOption(code) {
    open.value = false;

    if (code === props.modelValue) {
        return;
    }

    const selected = props.options.find((item) => item.code === code);
    const storageKey = window.__PORTFOTILO_LOCALE_KEY || STORAGE_KEY;

    localStorage.setItem(storageKey, code);

    if (selected?.href) {
        window.location.assign(selected.href);
        return;
    }

    if (typeof window.__setUiLocale === 'function') {
        window.__setUiLocale(code);
        return;
    }

    const url = new URL(window.location.href);
    url.searchParams.set('locale', code);
    window.location.assign(url.toString());
}

function onClickOutside(event) {
    if (rootEl.value && !rootEl.value.contains(event.target)) {
        open.value = false;
    }
}

function onEscape(event) {
    if (event.key === 'Escape') {
        open.value = false;
    }
}

onMounted(() => {
    document.addEventListener('click', onClickOutside);
    document.addEventListener('keydown', onEscape);
});

onBeforeUnmount(() => {
    document.removeEventListener('click', onClickOutside);
    document.removeEventListener('keydown', onEscape);
});
</script>

<template>
    <div ref="rootEl" class="relative inline-block">
        <button type="button" role="combobox" aria-haspopup="listbox" :aria-expanded="open" aria-label="Locale"
            class="flex items-center gap-1.5 rounded-lg border border-border bg-card px-2.5 py-1.5 text-sm font-medium text-foreground shadow-sm transition-colors hover:bg-accent"
            @click="open = !open">
            <span>{{ currentLabel }}</span>
            <svg class="h-4 w-4 shrink-0 text-muted-foreground transition-transform duration-150"
                :class="{ 'rotate-180': open }" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke-width="2"
                stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
            </svg>
        </button>

        <Transition enter-active-class="transition ease-out duration-100" enter-from-class="opacity-0 scale-95"
            enter-to-class="opacity-100 scale-100" leave-active-class="transition ease-in duration-75"
            leave-from-class="opacity-100 scale-100" leave-to-class="opacity-0 scale-95">
            <ul v-if="open" role="listbox"
                class="absolute right-0 z-50 mt-1.5 w-40 overflow-hidden rounded-xl border border-border bg-card py-1 shadow-lg">
                <li v-for="item in options" :key="item.code" role="option" :aria-selected="item.code === modelValue"
                    class="flex cursor-pointer items-center justify-between px-3 py-2 text-sm text-foreground transition-colors hover:bg-accent"
                    :class="{ 'font-semibold text-primary': item.code === modelValue }"
                    @click="selectOption(item.code)">
                    <span>{{ labelFor(item) }}</span>
                    <svg v-if="item.code === modelValue" class="h-4 w-4 text-primary" width="16" height="16" fill="none"
                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                    </svg>
                </li>
            </ul>
        </Transition>
    </div>
</template>