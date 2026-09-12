<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3';
import PublicLayout from '../Layouts/PublicLayout.vue';
import ParticleBackground from '../Components/ParticleBackground.vue';
import { computed } from 'vue';

const page = usePage();
const ui = computed(() => page.props.ui?.landing || {});
const user = computed(() => page.props.auth?.user || null);
const workspaceUrl = computed(() => (user.value?.is_admin ? '/admin' : '/studio/setup'));

defineProps({
    demoUrl: String,
});
</script>

<template>
    <PublicLayout>
        <Head :title="ui.title || 'Portfotilo'" />
        <section class="relative flex min-h-[calc(100vh-5rem)] items-center overflow-hidden">
            <ParticleBackground />
            <div class="absolute inset-0 bg-gradient-hero" />
            <div class="section-container relative z-10 py-16">
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
                        <Link href="/register" class="btn-primary">{{ ui.cta }}</Link>
                        <Link href="/login" class="btn-outline">{{ ui.login }}</Link>
                    </template>
                    <Link v-if="demoUrl" :href="demoUrl" class="btn-outline">{{ ui.demo }}</Link>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
