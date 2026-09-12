<script setup>
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import LocaleSelect from '../Components/LocaleSelect.vue';
import ScrollProgress from '../Components/ScrollProgress.vue';
import ScrollToTop from '../Components/ScrollToTop.vue';

const page = usePage();
const ui = computed(() => page.props.ui || {});
const authUi = computed(() => ui.value.auth || {});
const user = computed(() => page.props.auth?.user || null);
const locale = computed(() => page.props.locale);
const locales = computed(() => page.props.locales || []);
const appearance = computed(() => page.props.appearance || null);
const defaultMode = computed(() => page.props.theme || 'system');
const isDark = ref(false);
const menuOpen = ref(false);
const scrolled = ref(false);
const logoutForm = useForm({});
let onScroll = null;

function cssBlock(vars) {
    return Object.entries(vars || {})
        .map(([key, value]) => `${key}:${value}`)
        .join(';');
}

function applyAppearance(theme) {
    const root = document.documentElement;
    let styleEl = document.getElementById('pf-appearance');

    if (!theme) {
        ['layout', 'hero', 'radius', 'font', 'density'].forEach((key) => root.removeAttribute(`data-${key}`));
        if (styleEl) {
            styleEl.textContent = '';
        }
        return;
    }

    root.dataset.layout = theme.layout || 'centered';
    root.dataset.hero = theme.hero || 'particles';
    root.dataset.radius = theme.radius || 'lg';
    root.dataset.font = theme.font || 'sans';
    root.dataset.density = theme.density || 'comfortable';

    if (!styleEl) {
        styleEl = document.createElement('style');
        styleEl.id = 'pf-appearance';
        document.head.appendChild(styleEl);
    }

    styleEl.textContent = `:root{${cssBlock(theme.css)}}.dark{${cssBlock(theme.dark_css)}}`;
}

function applyTheme(theme, persist = true) {
    if (persist) {
        localStorage.setItem('portfotilo-theme', theme);
    }
    const dark =
        theme === 'dark' ||
        (theme === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches);
    document.documentElement.classList.toggle('dark', dark);
    isDark.value = dark;
}

function currentMode() {
    return localStorage.getItem('portfotilo-theme') || defaultMode.value || 'system';
}

function toggleTheme() {
    applyTheme(isDark.value ? 'light' : 'dark');
}

function logout() {
    logoutForm.post('/logout', { preserveState: false });
}

function closeMenu() {
    menuOpen.value = false;
}

watch(appearance, (theme) => applyAppearance(theme), { immediate: true, deep: true });
watch(defaultMode, () => applyTheme(currentMode(), false));

onMounted(() => {
    applyAppearance(appearance.value);
    applyTheme(currentMode(), false);
    onScroll = () => {
        scrolled.value = window.scrollY > 40;
    };
    onScroll();
    window.addEventListener('scroll', onScroll, { passive: true });
});

watch(menuOpen, (open) => {
    document.body.style.overflow = open ? 'hidden' : '';
});

onBeforeUnmount(() => {
    document.body.style.overflow = '';
    if (onScroll) {
        window.removeEventListener('scroll', onScroll);
    }
});
</script>

<template>
    <div class="min-h-screen bg-background text-foreground">
        <ScrollProgress />
        <header
            class="fixed inset-x-0 top-0 z-50 print:hidden transition-all duration-300"
            :class="scrolled || menuOpen ? 'border-b border-border bg-background/95 backdrop-blur-lg' : ''"
        >
            <div class="section-container flex h-16 min-w-0 items-center justify-between gap-3 md:h-20">
                <div class="min-w-0 truncate font-heading text-lg font-bold md:text-xl">
                    <slot name="brand">
                        <a href="/"><span class="text-gradient">Port</span>fotilo</a>
                    </slot>
                </div>
                <div class="site-nav-desktop hidden min-w-0 items-center gap-3 text-sm text-muted-foreground lg:flex lg:flex-wrap xl:gap-5">
                    <slot name="nav" />
                    <template v-if="user">
                        <a v-if="user.is_admin" href="/admin" class="hover:text-foreground">{{ authUi.admin }}</a>
                        <a href="/studio/setup" class="hover:text-foreground">{{ authUi.studio }}</a>
                        <button type="button" class="hover:text-foreground" :disabled="logoutForm.processing" @click="logout">
                            {{ authUi.logout }}
                        </button>
                    </template>
                    <template v-else>
                        <Link href="/login" class="hover:text-foreground">{{ authUi.login }}</Link>
                        <Link href="/register" class="font-medium text-primary">{{ authUi.register }}</Link>
                    </template>
                    <button
                        type="button"
                        class="rounded-lg p-2 text-muted-foreground hover:bg-muted hover:text-foreground"
                        :aria-label="isDark ? (ui.theme_light || 'Light') : (ui.theme_dark || 'Dark')"
                        @click="toggleTheme"
                    >
                        <svg v-if="isDark" class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <circle cx="12" cy="12" r="4" />
                            <path d="M12 3v1.5M12 19.5V21M4.5 12H3M21 12h-1.5M6.2 6.2 5.1 5.1M18.9 18.9l-1.1-1.1M17.8 6.2l1.1-1.1M6.2 17.8 5.1 18.9" />
                        </svg>
                        <svg v-else class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M20 14.5A8.5 8.5 0 1 1 9.5 4 7 7 0 0 0 20 14.5Z" />
                        </svg>
                    </button>
                    <slot name="locale">
                        <LocaleSelect v-if="locales.length" :model-value="locale" :options="locales" />
                    </slot>
                </div>
                <div class="site-nav-mobile flex shrink-0 items-center gap-2 lg:hidden">
                    <slot name="locale">
                        <LocaleSelect v-if="locales.length" :model-value="locale" :options="locales" />
                    </slot>
                    <button
                        type="button"
                        class="rounded-lg p-2 text-muted-foreground hover:bg-muted hover:text-foreground"
                        @click="toggleTheme"
                    >
                        <svg v-if="isDark" class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <circle cx="12" cy="12" r="4" />
                            <path d="M12 3v1.5M12 19.5V21M4.5 12H3M21 12h-1.5M6.2 6.2 5.1 5.1M18.9 18.9l-1.1-1.1M17.8 6.2l1.1-1.1M6.2 17.8 5.1 18.9" />
                        </svg>
                        <svg v-else class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M20 14.5A8.5 8.5 0 1 1 9.5 4 7 7 0 0 0 20 14.5Z" />
                        </svg>
                    </button>
                    <button type="button" class="p-2" @click="menuOpen = !menuOpen">☰</button>
                </div>
            </div>
            <div v-if="menuOpen" class="site-nav-panel border-t border-border bg-card px-4 py-4 lg:hidden" @click="closeMenu">
                <div class="flex flex-col items-center gap-3 text-sm">
                    <slot name="nav" />
                    <template v-if="user">
                        <a v-if="user.is_admin" href="/admin">{{ authUi.admin }}</a>
                        <a href="/studio/setup">{{ authUi.studio }}</a>
                        <button type="button" @click="logout">{{ authUi.logout }}</button>
                    </template>
                    <template v-else>
                        <Link href="/login">{{ authUi.login }}</Link>
                        <Link href="/register" class="text-primary">{{ authUi.register }}</Link>
                    </template>
                </div>
            </div>
        </header>
        <main class="pt-16 md:pt-20">
            <slot />
        </main>
        <footer class="border-t border-border py-8 text-sm text-muted-foreground print:hidden">
            <div class="section-container flex flex-col items-center justify-between gap-3 sm:flex-row">
                <slot name="footer">© {{ new Date().getFullYear() }} Portfotilo</slot>
            </div>
        </footer>
        <ScrollToTop />
    </div>
</template>
