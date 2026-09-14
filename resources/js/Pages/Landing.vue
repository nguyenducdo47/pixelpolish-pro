<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3';
import PublicLayout from '../Layouts/PublicLayout.vue';
import ParticleBackground from '../Components/ParticleBackground.vue';
import { computed } from 'vue';

const props = defineProps({
    demoUrl: String,
    activeProfile: {
        type: String,
        default: 'general',
    },
    profileOptions: {
        type: Array,
        default: () => [],
    },
});

const page = usePage();
const fallbackUi = computed(() => page.props.ui?.landing || {});
const user = computed(() => page.props.auth?.user || null);
const workspaceUrl = computed(() => (user.value?.is_admin ? '/admin' : '/studio/setup'));

const selectedKey = computed(() => props.activeProfile || 'general');

const activeOption = computed(() => {
    const match = props.profileOptions.find((item) => item.key === selectedKey.value);
    return match || props.profileOptions[0] || null;
});

const ui = computed(() => activeOption.value?.landing || fallbackUi.value);
const demoLink = computed(() => activeOption.value?.demo_url || props.demoUrl);
</script>

<template>
    <PublicLayout>
        <Head :title="ui.title || 'Portfotilo'" />
        <section class="relative flex min-h-[calc(100vh-5rem)] items-center overflow-hidden">
            <ParticleBackground />
            <div class="absolute inset-0 bg-gradient-hero" />
            <div class="section-container relative z-10 py-16">
                <div v-if="profileOptions.length" class="mb-6 flex flex-wrap gap-2">
                    <Link
                        v-for="option in profileOptions"
                        :key="option.key"
                        :href="`/?profile=${option.key}`"
                        class="rounded-full border px-3 py-1 text-xs font-medium transition-colors"
                        :class="
                            option.key === selectedKey
                                ? 'border-primary bg-primary/10 text-primary'
                                : 'border-border bg-secondary text-muted-foreground hover:border-primary/40'
                        "
                    >
                        {{ option.label }}
                    </Link>
                </div>
                <p class="inline-block rounded-full border border-border bg-secondary px-4 py-1.5 text-xs font-medium uppercase tracking-wider text-muted-foreground">
                    {{ ui.badge }}
                </p>
                <h1 class="mt-6 max-w-3xl text-4xl font-bold tracking-tight sm:text-5xl md:text-6xl">
                    {{ ui.title }}
                </h1>
                <p class="mt-5 max-w-2xl text-lg text-muted-foreground">
                    {{ ui.subtitle }}
                </p>
                <div class="mt-8 flex flex-wrap gap-3">
                    <a v-if="user" :href="workspaceUrl" class="btn-primary">{{ ui.studio }}</a>
                    <template v-else>
                        <Link :href="`/register?profile=${selectedKey}`" class="btn-primary">{{ ui.cta }}</Link>
                        <Link :href="`/login?profile=${selectedKey}`" class="btn-outline">{{ ui.login }}</Link>
                    </template>
                    <Link v-if="demoLink" :href="demoLink" class="btn-outline">{{ ui.demo }}</Link>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
