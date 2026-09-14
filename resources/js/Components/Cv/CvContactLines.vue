<script setup>
import { computed } from 'vue';
import CvContactIcon from './CvContactIcon.vue';

const props = defineProps({
    profile: { type: Object, default: () => ({}) },
    github: { type: Object, default: null },
    inverted: { type: Boolean, default: false },
    /** default | modern-grid | classic-inline */
    variant: { type: String, default: 'default' },
});

const contactItems = computed(() => {
    const items = [];
    const p = props.profile || {};

    if (p.date_of_birth) {
        items.push({ type: 'calendar', label: p.date_of_birth });
    }
    if (p.email) {
        items.push({ type: 'envelope', label: p.email, href: `mailto:${p.email}` });
    }
    if (p.phone) {
        items.push({ type: 'phone', label: p.phone, href: `tel:${p.phone}` });
    }
    if (p.location) {
        items.push({ type: 'location', label: p.location });
    }
    if (p.website) {
        items.push({
            type: 'website',
            label: p.website.replace(/^https?:\/\//, ''),
            href: p.website,
            external: true,
        });
    }
    if (props.github?.url) {
        items.push({
            type: 'github',
            label: props.github.url.replace(/^https?:\/\//, ''),
            href: props.github.url,
            external: true,
        });
    }

    return items;
});

const iconClass = computed(() => (props.inverted ? 'h-3.5 w-3.5 shrink-0 text-white/75' : 'h-3.5 w-3.5 shrink-0 opacity-80'));

const lineClass = computed(() => {
    if (props.inverted) {
        return 'flex items-center gap-2 break-all text-[13px] text-white/90';
    }

    if (props.variant === 'modern-grid') {
        return 'flex items-center gap-2 break-all text-xs text-muted-foreground hover:text-foreground print:text-gray-600';
    }

    if (props.variant === 'classic-inline') {
        return 'inline-flex items-center gap-1.5 break-all text-[13px] text-muted-foreground hover:text-foreground print:text-gray-600';
    }

    return 'flex items-center gap-1.5 break-all text-sm text-muted-foreground hover:text-foreground print:text-gray-600';
});
</script>

<template>
    <div v-if="variant === 'classic-inline'" class="cv-classic-contact">
        <template v-for="(item, index) in contactItems" :key="`${item.type}-${index}`">
            <component :is="item.href ? 'a' : 'span'" :href="item.href || undefined"
                :target="item.external ? '_blank' : undefined" :class="lineClass">
                <CvContactIcon :type="item.type" :class="iconClass" />
                {{ item.label }}
            </component>
        </template>
    </div>

    <div v-else-if="variant === 'modern-grid'" class="cv-modern-contact-grid">
        <component v-for="(item, index) in contactItems" :key="`${item.type}-${index}`"
            :is="item.href ? 'a' : 'span'" :href="item.href || undefined"
            :target="item.external ? '_blank' : undefined" :class="lineClass">
            <CvContactIcon :type="item.type" :class="iconClass" />
            {{ item.label }}
        </component>
    </div>

    <div v-else class="flex flex-col gap-2">
        <component v-for="(item, index) in contactItems" :key="`${item.type}-${index}`"
            :is="item.href ? 'a' : 'span'" :href="item.href || undefined"
            :target="item.external ? '_blank' : undefined" :class="lineClass">
            <CvContactIcon :type="item.type" :class="iconClass" />
            {{ item.label }}
        </component>
    </div>
</template>
