<script setup>
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import PublicLayout from '../../Layouts/PublicLayout.vue';
import { computed } from 'vue';

const props = defineProps({
    initialProfile: String,
    registerProfile: Object,
});

const page = usePage();
const ui = computed(() => page.props.ui?.auth || {});

const form = useForm({
    name: '',
    username: '',
    email: '',
    password: '',
    password_confirmation: '',
    content_profile: props.initialProfile || '',
    marketing_opt_in: false,
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
                <p
                    v-if="registerProfile?.label"
                    class="rounded-lg border border-border bg-muted/40 px-3 py-2 text-sm text-muted-foreground"
                >
                    {{ ui.register_profile_prefix }} <span class="font-medium text-foreground">{{ registerProfile.label }}</span>
                </p>
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
                <label class="flex items-start gap-2 text-sm">
                    <input
                        v-model="form.marketing_opt_in"
                        type="checkbox"
                        class="mt-1 rounded border-border"
                    />
                    <span>
                        <span class="font-medium">{{ ui.marketing_opt_in }}</span>
                        <span class="mt-1 block text-xs text-muted-foreground">{{ ui.marketing_opt_in_hint }}</span>
                    </span>
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
                    <Link :href="initialProfile ? `/login?profile=${initialProfile}` : '/login'" class="form-link">{{ ui.login }}</Link>
                </p>
            </form>
        </section>
    </PublicLayout>
</template>
