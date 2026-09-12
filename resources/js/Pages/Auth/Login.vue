<script setup>
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import PublicLayout from '../../Layouts/PublicLayout.vue';
import { computed } from 'vue';

const page = usePage();
const ui = computed(() => page.props.ui?.auth || {});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

function submit() {
    form.post('/login', { preserveState: false });
}
</script>

<template>
    <PublicLayout>
        <Head :title="ui.login_title || 'Log in'" />
        <section class="container-page flex min-h-[70vh] items-center py-16">
            <form class="mx-auto w-full max-w-md space-y-4" @submit.prevent="submit">
                <h1 class="text-2xl font-semibold tracking-tight">{{ ui.login_title }}</h1>
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
                <label class="block text-sm">
                    <span class="font-medium">{{ ui.password }}</span>
                    <input
                        v-model="form.password"
                        type="password"
                        required
                        autocomplete="current-password"
                        class="form-input"
                    />
                    <p v-if="form.errors.password" class="mt-1 text-sm text-red-600">{{ form.errors.password }}</p>
                </label>
                <label class="flex items-center justify-between gap-3 text-sm">
                    <span class="inline-flex items-center gap-2">
                        <input v-model="form.remember" type="checkbox" class="rounded border-border" />
                        {{ ui.remember }}
                    </span>
                    <Link href="/forgot-password" class="form-link">{{ ui.forgot_password }}</Link>
                </label>
                <button
                    type="submit"
                    class="btn-primary w-full"
                    :disabled="form.processing"
                >
                    {{ ui.submit_login }}
                </button>
                <p class="text-sm text-muted-foreground">
                    {{ ui.no_account }}
                    <Link href="/register" class="form-link">{{ ui.register }}</Link>
                </p>
            </form>
        </section>
    </PublicLayout>
</template>
