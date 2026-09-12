<script setup>
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import PublicLayout from '../../Layouts/PublicLayout.vue';
import { computed } from 'vue';

const page = usePage();
const ui = computed(() => page.props.ui?.auth || {});
const status = computed(() => page.props.status || '');

const form = useForm({
    email: '',
});

function submit() {
    form.post('/forgot-password');
}
</script>

<template>
    <PublicLayout>
        <Head :title="ui.forgot_title || 'Forgot password'" />
        <section class="container-page flex min-h-[70vh] items-center py-16">
            <form class="mx-auto w-full max-w-md space-y-4" @submit.prevent="submit">
                <h1 class="text-2xl font-semibold tracking-tight">{{ ui.forgot_title }}</h1>
                <p class="text-sm text-muted-foreground">{{ ui.forgot_help }}</p>
                <p
                    v-if="status"
                    class="rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm text-emerald-800 dark:border-emerald-900 dark:bg-emerald-950 dark:text-emerald-200"
                >
                    {{ status }}
                </p>
                <label class="block text-sm">
                    <span class="font-medium">{{ ui.email }}</span>
                    <input
                        v-model="form.email"
                        type="email"
                        required
                        autocomplete="email"
                        class="form-input"
                    />
                    <p v-if="form.errors.email" class="mt-1 text-sm text-red-600">{{ form.errors.email }}</p>
                </label>
                <button
                    type="submit"
                    class="btn-primary w-full"
                    :disabled="form.processing"
                >
                    {{ ui.forgot_submit }}
                </button>
                <p class="text-sm text-muted-foreground">
                    <Link href="/login" class="form-link">{{ ui.back_to_login }}</Link>
                </p>
            </form>
        </section>
    </PublicLayout>
</template>
