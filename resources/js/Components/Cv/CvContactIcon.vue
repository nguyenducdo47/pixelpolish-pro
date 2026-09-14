<script setup>
defineProps({
    type: {
        type: String,
        required: true,
    },
    class: {
        type: String,
        default: 'h-3.5 w-3.5 shrink-0 opacity-80',
    },
    filled: {
        type: Boolean,
        default: false,
    },
});
</script>

<template>
    <svg v-if="type === 'calendar'" :class="$props.class" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
        stroke="currentColor" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round"
            d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
    </svg>

    <svg v-else-if="type === 'envelope'" :class="$props.class" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
        stroke="currentColor" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round"
            d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
    </svg>

    <svg v-else-if="type === 'phone'" :class="$props.class" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
        stroke="currentColor" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round"
            d="M2.25 6.75c0 8.284 6.716 15 15 15h1.5a2.25 2.25 0 0 0 2.25-2.25v-1.372a1 1 0 0 0-.628-.929l-4.417-1.767a1 1 0 0 0-1.185.322l-.573.765a2.25 2.25 0 0 1-2.847.598 11.25 11.25 0 0 1-4.5-4.5 2.25 2.25 0 0 1 .598-2.847l.765-.573a1 1 0 0 0 .322-1.185L7.918 3.128a1 1 0 0 0-.928-.628H5.625a2.25 2.25 0 0 0-2.25 2.25v.001z" />
    </svg>

    <svg v-else-if="type === 'location'" :class="$props.class" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
        stroke="currentColor" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
        <path stroke-linecap="round" stroke-linejoin="round"
            d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
    </svg>

    <svg v-else-if="type === 'website'" :class="$props.class" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
        stroke="currentColor" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18Z" />
        <path stroke-linecap="round" stroke-linejoin="round"
            d="M3.6 9h16.8M3.6 15h16.8M11.25 3a17.25 17.25 0 0 0 0 18M12.75 3a17.25 17.25 0 0 1 0 18" />
    </svg>

    <svg v-else-if="type === 'github'" :class="$props.class" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
        <path fill-rule="evenodd" clip-rule="evenodd"
            d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.833.092-.647.35-1.088.636-1.339-2.221-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.269 2.75 1.026A9.564 9.564 0 0 1 12 6.844c.85.004 1.705.115 2.504.337 1.909-1.295 2.747-1.026 2.747-1.026.546 1.378.203 2.397.1 2.65.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.919.678 1.852 0 1.336-.012 2.415-.012 2.743 0 .268.18.58.688.482A10.02 10.02 0 0 0 22 12.017C22 6.484 17.522 2 12 2Z" />
    </svg>
</template>
