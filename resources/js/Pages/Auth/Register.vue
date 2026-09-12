<script setup>
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import PublicLayout from '../../Layouts/PublicLayout.vue';
import { computed } from 'vue';

const page = usePage();
const ui = computed(() => page.props.ui?.auth || {});

const form = useForm({
    name: '',
    username: '',
    email: '',
    password: '',
    password_confirmation: '',
});

function submit() {
    form.post('/register', { preserveState: false });
}
</script>

<template>
    <PublicLayout>
        <Head :title="ui.register_title || 'Register'" />
        <section class="container-page flex min-h-[70vh] items-center py-16">
            <form class="mx-auto w-full max-w-md space-y-4" @submit.prevent="submit">
                <h1 class="text-2xl font-semibold tracking-tight">{{ ui.register_title }}</h1>
                <label class="block text-sm">
                    <span class="font-medium">{{ ui.name }}</span>
                    <input
                        v-model="form.name"
                        type="text"
                        required
                        autocomplete="name"
                        class="form-input"
                    />
                    <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</p>
                </label>
                <label class="block text-sm">
                    <span class="font-medium">{{ ui.username }}</span>
                    <input
                        v-model="form.username"
                        type="text"
                        required
                        autocomplete="username"
                        class="form-input"
                    />
                    <p class="mt-1 text-xs text-muted-foreground">{{ ui.username_hint }}</p>
                    <p v-if="form.errors.username" class="mt-1 text-sm text-red-600">{{ form.errors.username }}</p>
                </label>
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
                        autocomplete="new-password"
                        class="form-input"
                    />
                    <p v-if="form.errors.password" class="mt-1 text-sm text-red-600">{{ form.errors.password }}</p>
                </label>
                <label class="block text-sm">
                    <span class="font-medium">{{ ui.password_confirmation }}</span>
                    <input
                        v-model="form.password_confirmation"
                        type="password"
                        required
                        autocomplete="new-password"
                        class="form-input"
                    />
                </label>
                <button
                    type="submit"
                    class="btn-primary w-full"
                    :disabled="form.processing"
                >
                    {{ ui.submit_register }}
                </button>
                <p class="text-sm text-muted-foreground">
                    {{ ui.has_account }}
                    <Link href="/login" class="form-link">{{ ui.login }}</Link>
                </p>
            </form>
        </section>
    </PublicLayout>
</template>
