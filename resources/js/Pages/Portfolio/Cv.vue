<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import HtmlContent from '../../Components/HtmlContent.vue';
import LocaleSelect from '../../Components/LocaleSelect.vue';
import PublicLayout from '../../Layouts/PublicLayout.vue';

const props = defineProps({
    locale: String,
    username: String,
    profile: Object,
    skill_categories: Array,
    projects: Array,
    education: Array,
    languages: Array,
    principles: Array,
    seo: Object,
    cv: Object,
    available_locales: Array,
    social_links: Array,
});

const page = usePage();
const ui = computed(() => page.props.ui || {});
const settings = computed(() => props.cv?.settings || {});
const appearance = computed(() => page.props.appearance || {});
const cvLayout = computed(() => appearance.value.cv_layout || settings.value.template || 'modern');
const portfolioUrl = computed(
    () => (props.available_locales || []).find((item) => item.code === props.locale)?.portfolio_url || '/',
);
const github = computed(() => (props.social_links || []).find((link) => String(link.platform).toLowerCase().includes('git')));

function printCv() {
    window.print();
}
</script>

<template>
    <PublicLayout>
        <template #brand>
            <Link :href="portfolioUrl" class="text-sm text-muted-foreground hover:text-foreground">
                ← {{ ui.cv?.back }}
            </Link>
        </template>
        <template #nav>
            <button type="button" class="btn-outline px-3 py-1.5 text-xs" @click="printCv">
                {{ ui.cv?.print }}
            </button>
            <a :href="cv.pdf_url" class="btn-primary px-3 py-1.5 text-xs">{{ ui.cv?.download }}</a>
        </template>
        <template #locale>
            <LocaleSelect :model-value="locale"
                :options="(available_locales || []).map((item) => ({ ...item, href: item.cv_url }))" />
        </template>
        <template #footer>
            {{ profile?.full_name }}
        </template>

        <Head :title="`${profile?.full_name} · CV`" />

        <div class="min-h-screen bg-muted/30 pb-8 print:bg-white print:pt-0 print:pb-0">
            <article class="cv-sheet mx-auto w-full max-w-[210mm] bg-background shadow-xl print:shadow-none"
                :class="`cv-${cvLayout}`">
                <div class="cv-body space-y-7 p-4 text-justify sm:p-10 md:p-12 print:space-y-4 print:p-[15mm]">
                    <header
                        class="cv-header flex flex-col gap-3 border-b-2 border-primary pb-4 lg:flex-row lg:items-start lg:justify-between print:border-black">
                        <div class="flex min-w-0 items-start gap-4">
                            <img v-if="settings.show_avatar && profile?.avatar" :src="profile.avatar" alt=""
                                class="h-16 w-16 shrink-0 rounded-full object-cover sm:h-20 sm:w-20 print:h-16 print:w-16" />
                            <div class="min-w-0">
                                <h1 class="text-2xl font-bold tracking-tight break-words sm:text-3xl print:text-2xl">{{
                                    profile?.full_name }}</h1>
                                <p
                                    class="mt-1 text-base font-semibold text-primary break-words sm:text-lg print:text-base print:text-gray-700">
                                    {{ profile?.headline }}
                                </p>
                            </div>
                        </div>
                        <div
                            class="flex min-w-0 flex-col gap-1 break-words text-sm text-muted-foreground print:text-gray-600">
                            <span v-if="profile?.date_of_birth" class="flex items-center gap-1.5">
                                <svg class="h-3.5 w-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                                </svg>
                                {{ profile.date_of_birth }}
                            </span>

                            <a v-if="profile?.email" :href="`mailto:${profile.email}`"
                                class="flex items-center gap-1.5 break-all hover:text-foreground">
                                <svg class="h-3.5 w-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                                </svg>
                                {{ profile.email }}
                            </a>

                            <a v-if="profile?.phone" :href="`tel:${profile.phone}`"
                                class="flex items-center gap-1.5 hover:text-foreground">
                                <svg class="h-3.5 w-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M2.25 6.75c0 8.284 6.716 15 15 15h1.5a2.25 2.25 0 0 0 2.25-2.25v-1.372a1 1 0 0 0-.628-.929l-4.417-1.767a1 1 0 0 0-1.185.322l-.573.765a2.25 2.25 0 0 1-2.847.598 11.25 11.25 0 0 1-4.5-4.5 2.25 2.25 0 0 1 .598-2.847l.765-.573a1 1 0 0 0 .322-1.185L7.918 3.128a1 1 0 0 0-.928-.628H5.625a2.25 2.25 0 0 0-2.25 2.25v.001z" />
                                </svg>
                                {{ profile.phone }}
                            </a>

                            <span v-if="profile?.location" class="flex items-center gap-1.5 break-words">
                                <svg class="h-3.5 w-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                                </svg>
                                {{ profile.location }}
                            </span>

                            <a v-if="profile?.website" :href="profile.website" target="_blank"
                                class="flex items-center gap-1.5 break-all hover:text-foreground">
                                <svg class="h-3.5 w-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3.6 9h16.8M3.6 15h16.8M11.25 3a17.25 17.25 0 0 0 0 18M12.75 3a17.25 17.25 0 0 1 0 18" />
                                </svg>
                                {{ profile.website.replace(/^https?:\/\//, '') }}
                            </a>

                            <a v-if="github?.url" :href="github.url" target="_blank"
                                class="flex items-center gap-1.5 break-all hover:text-foreground">
                                <svg class="h-3.5 w-3.5 shrink-0" fill="currentColor" viewBox="0 0 24 24"
                                    aria-hidden="true">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.833.092-.647.35-1.088.636-1.339-2.221-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.269 2.75 1.026A9.564 9.564 0 0 1 12 6.844c.85.004 1.705.115 2.504.337 1.909-1.295 2.747-1.026 2.747-1.026.546 1.378.203 2.397.1 2.65.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.919.678 1.852 0 1.336-.012 2.415-.012 2.743 0 .268.18.58.688.482A10.02 10.02 0 0 0 22 12.017C22 6.484 17.522 2 12 2Z" />
                                </svg>
                                {{ github.url.replace(/^https?:\/\//, '') }}
                            </a>
                        </div>
                    </header>

                    <section v-if="settings.show_about && profile?.about" class="cv-main">
                        <h2
                            class="mb-2 border-b border-border pb-1 text-sm font-bold tracking-widest uppercase print:border-gray-300">
                            {{ ui.cv?.summary }}
                        </h2>
                        <HtmlContent class="text-sm leading-loose text-muted-foreground print:text-gray-700"
                            :html="profile.about" />
                    </section>

                    <section v-if="settings.show_education && education?.length" class="cv-main">
                        <h2
                            class="mb-2 border-b border-border pb-1 text-sm font-bold tracking-widest uppercase print:border-gray-300">
                            {{ ui.cv?.education }}
                        </h2>
                        <div v-for="item in education" :key="item.degree" class="mb-2">
                            <div class="flex flex-col gap-1 sm:flex-row sm:items-baseline sm:justify-between sm:gap-3">
                                <h3 class="min-w-0 text-sm font-semibold break-words">{{ item.degree }}</h3>
                                <span class="shrink-0 text-xs text-muted-foreground">{{ item.period }}</span>
                            </div>
                            <p class="text-xs text-muted-foreground">{{ item.school }}</p>
                            <HtmlContent v-if="item.details" class="mt-0.5 text-xs text-muted-foreground"
                                :html="item.details" />
                        </div>
                    </section>

                    <section v-if="settings.show_skills && skill_categories?.length" class="cv-side">
                        <h2
                            class="mb-2 border-b border-border pb-1 text-sm font-bold tracking-widest uppercase print:border-gray-300">
                            {{ ui.cv?.skills }}
                        </h2>
                        <div class="space-y-1.5">
                            <div v-for="category in skill_categories" :key="category.name"
                                class="flex flex-col text-sm print:text-xs">
                                <span class="cv-skill-label font-semibold break-words">{{ category.name }}:</span>
                                <span class="text-muted-foreground break-words">{{category.skills.map((s) =>
                                    s.name).join(', ')
                                }}</span>
                            </div>
                        </div>
                    </section>

                    <section v-if="settings.show_projects && projects?.length" class="cv-main">
                        <h2
                            class="mb-3 border-b border-border pb-1 text-sm font-bold tracking-widest uppercase print:border-gray-300">
                            {{ ui.cv?.experience }}
                        </h2>
                        <div class="space-y-5 print:space-y-4">
                            <div v-for="project in projects" :key="project.title">
                                <div class="flex flex-wrap items-baseline justify-between gap-1">
                                    <h3 class="min-w-0 text-[13px] font-semibold break-words">{{ project.title }}</h3>
                                    <span class="text-[10px] text-muted-foreground">{{ project.period }}</span>
                                </div>
                                <p v-if="project.subtitle" class="text-xs text-muted-foreground">{{ project.subtitle }}
                                </p>
                                <HtmlContent v-if="project.summary || project.solution"
                                    class="mt-1 text-xs text-muted-foreground"
                                    :html="project.summary || project.solution" />
                                <ul v-if="project.highlights?.length" class="mt-1.5 space-y-1">
                                    <li v-for="item in project.highlights" :key="item"
                                        class="flex items-start gap-1.5 text-xs text-muted-foreground">
                                        <span>•</span>
                                        <span>{{ item }}</span>
                                    </li>
                                </ul>
                                <div class="mt-1.5 flex flex-wrap gap-1">
                                    <span v-for="tech in project.tech_stack" :key="tech"
                                        class="rounded border border-border bg-secondary px-1.5 py-0.5 text-[10px] font-medium print:border-gray-300 print:bg-gray-100">
                                        {{ tech }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section v-if="settings.show_languages && languages?.length" class="cv-side">
                        <h2
                            class="mb-2 border-b border-border pb-1 text-sm font-bold tracking-widest uppercase print:border-gray-300">
                            {{ ui.cv?.languages }}
                        </h2>
                        <div class="flex flex-wrap gap-x-6 gap-y-1">
                            <div v-for="item in languages" :key="item.name" class="text-sm print:text-xs">
                                <span class="font-semibold">{{ item.name }}</span>
                                <span class="text-muted-foreground"> — {{ item.level }}</span>
                            </div>
                        </div>
                    </section>

                    <section v-if="settings.show_principles && principles?.length" class="cv-main">
                        <h2
                            class="mb-2 border-b border-border pb-1 text-sm font-bold tracking-widest uppercase print:border-gray-300">
                            {{ ui.philosophy?.title }}
                        </h2>
                        <div v-for="item in principles" :key="item.title" class="mb-1 text-sm print:text-xs">
                            <span class="font-semibold">{{ item.title }}</span>
                            <span v-if="item.description" class="text-muted-foreground"> — </span>
                            <HtmlContent v-if="item.description" class="inline text-muted-foreground"
                                :html="item.description" />
                        </div>
                    </section>
                </div>
            </article>
        </div>
    </PublicLayout>
</template>
