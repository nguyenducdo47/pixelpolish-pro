<script setup>
import { Head } from '@inertiajs/vue3';
import PublicLayout from '../../Layouts/PublicLayout.vue';

defineProps({
    title: { type: String, required: true },
    message: { type: String, required: true },
});
</script>

<template>
    <PublicLayout>
        <Head :title="title" />
        <section class="container-page flex min-h-[50vh] items-center py-16">
            <div class="mx-auto w-full max-w-md space-y-3 text-center">
                <h1 class="text-2xl font-semibold tracking-tight">{{ title }}</h1>
                <p class="text-sm text-muted-foreground">{{ message }}</p>
            </div>
        </section>
    </PublicLayout>
</template>
