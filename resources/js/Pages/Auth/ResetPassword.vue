<script setup>
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import PublicLayout from '../../Layouts/PublicLayout.vue';
import { computed } from 'vue';

const props = defineProps({
    token: { type: String, required: true },
    email: { type: String, default: '' },
});

const page = usePage();
const ui = computed(() => page.props.ui?.auth || {});

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

function submit() {
    form.post('/reset-password', { preserveState: false });
}
</script>

<template>
    <PublicLayout>
        <Head :title="ui.reset_title || 'Reset password'" />
        <section class="container-page flex min-h-[70vh] items-center py-16">
            <form class="mx-auto w-full max-w-md space-y-4" @submit.prevent="submit">
                <h1 class="text-2xl font-semibold tracking-tight">{{ ui.reset_title }}</h1>
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
                    {{ ui.reset_submit }}
                </button>
                <p class="text-sm text-muted-foreground">
                    <Link href="/login" class="form-link">{{ ui.back_to_login }}</Link>
                </p>
            </form>
        </section>
    </PublicLayout>
</template>
