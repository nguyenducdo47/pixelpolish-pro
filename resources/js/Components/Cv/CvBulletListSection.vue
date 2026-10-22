<script setup>
defineProps({
    title: { type: String, required: true },
    items: { type: Array, default: () => [] },
    variant: { type: String, default: 'modern' },
});
</script>

<template>
    <section v-if="items?.length"
        :class="{
            'cv-sidebar-block': variant === 'sidebar',
            'cv-classic-section': variant === 'classic',
            'cv-modern-section': variant === 'modern',
            'cv-main-block': variant === 'main',
        }">
        <h2 :class="{
            'cv-sidebar-heading': variant === 'sidebar',
            'cv-classic-section-title': variant === 'classic',
            'cv-modern-section-title': variant === 'modern',
            'cv-main-heading': variant === 'main',
        }">{{ title }}</h2>
        <ul :class="{
            'cv-classic-list': variant === 'classic',
            'list-disc space-y-1 pl-5 text-[13px] text-white/85': variant === 'sidebar',
            'list-disc space-y-1 pl-5 text-sm text-muted-foreground print:text-gray-700': variant !== 'classic' && variant !== 'sidebar',
        }">
            <li v-for="(item, index) in items" :key="`${item.title}-${index}`">{{ item.title }}</li>
        </ul>
    </section>
</template>
