<script setup>
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

function labelFor(item) {
    return item.native_name || item.label || String(item.code || '').toUpperCase();
}

function onChange(event) {
    const code = event.target.value;
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
</script>

<template>
    <label class="inline-flex items-center">
        <select
            aria-label="Locale"
            class="rounded-lg border border-border bg-card px-2 py-1 text-sm"
            :value="modelValue"
            @change="onChange"
        >
            <option v-for="item in options" :key="item.code" :value="item.code">
                {{ labelFor(item) }}
            </option>
        </select>
    </label>
</template>
