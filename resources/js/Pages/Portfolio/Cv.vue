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
            <LocaleSelect
                :model-value="locale"
                :options="(available_locales || []).map((item) => ({ ...item, href: item.cv_url }))"
            />
        </template>
        <template #footer>
            {{ profile?.full_name }}
        </template>

        <Head :title="`${profile?.full_name} · CV`" />

        <div class="min-h-screen bg-muted/30 pb-8 print:bg-white print:pt-0 print:pb-0">
            <article class="cv-sheet mx-auto w-full max-w-[210mm] bg-background shadow-xl print:shadow-none" :class="`cv-${cvLayout}`">
                <div class="cv-body space-y-7 p-4 text-justify sm:p-10 md:p-12 print:space-y-4 print:p-[15mm]">
                    <header class="cv-header flex flex-col gap-3 border-b-2 border-primary pb-4 lg:flex-row lg:items-start lg:justify-between print:border-black">
                        <div class="flex min-w-0 items-start gap-4">
                            <img
                                v-if="settings.show_avatar && profile?.avatar"
                                :src="profile.avatar"
                                alt=""
                                class="h-16 w-16 shrink-0 rounded-full object-cover sm:h-20 sm:w-20 print:h-16 print:w-16"
                            />
                            <div class="min-w-0">
                                <h1 class="text-2xl font-bold tracking-tight break-words sm:text-3xl print:text-2xl">{{ profile?.full_name }}</h1>
                                <p class="mt-1 text-base font-semibold text-primary break-words sm:text-lg print:text-base print:text-gray-700">
                                    {{ profile?.headline }}
                                </p>
                            </div>
                        </div>
                        <div class="flex min-w-0 flex-col gap-1 break-words text-sm text-muted-foreground print:text-gray-600">
                            <span v-if="profile?.date_of_birth">{{ profile.date_of_birth }}</span>
                            <a v-if="profile?.email" :href="`mailto:${profile.email}`" class="break-all hover:text-foreground">{{ profile.email }}</a>
                            <a v-if="profile?.phone" :href="`tel:${profile.phone}`" class="hover:text-foreground">{{ profile.phone }}</a>
                            <span v-if="profile?.location" class="break-words">{{ profile.location }}</span>
                            <a v-if="github?.url" :href="github.url" target="_blank" class="break-all hover:text-foreground">{{ github.url.replace(/^https?:\/\//, '') }}</a>
                        </div>
                    </header>

                    <section v-if="settings.show_about && profile?.about" class="cv-main">
                        <h2 class="mb-2 border-b border-border pb-1 text-sm font-bold tracking-widest uppercase print:border-gray-300">
                            {{ ui.cv?.summary }}
                        </h2>
                        <HtmlContent class="text-sm leading-loose text-muted-foreground print:text-gray-700" :html="profile.about" />
                    </section>

                    <section v-if="settings.show_education && education?.length" class="cv-main">
                        <h2 class="mb-2 border-b border-border pb-1 text-sm font-bold tracking-widest uppercase print:border-gray-300">
                            {{ ui.cv?.education }}
                        </h2>
                        <div v-for="item in education" :key="item.degree" class="mb-2">
                            <div class="flex flex-col gap-1 sm:flex-row sm:items-baseline sm:justify-between sm:gap-3">
                                <h3 class="min-w-0 text-sm font-semibold break-words">{{ item.degree }}</h3>
                                <span class="shrink-0 text-xs text-muted-foreground">{{ item.period }}</span>
                            </div>
                            <p class="text-xs text-muted-foreground">{{ item.school }}</p>
                            <HtmlContent v-if="item.details" class="mt-0.5 text-xs text-muted-foreground" :html="item.details" />
                        </div>
                    </section>

                    <section v-if="settings.show_skills && skill_categories?.length" class="cv-side">
                        <h2 class="mb-2 border-b border-border pb-1 text-sm font-bold tracking-widest uppercase print:border-gray-300">
                            {{ ui.cv?.skills }}
                        </h2>
                        <div class="space-y-1.5">
                            <div v-for="category in skill_categories" :key="category.name" class="flex flex-col text-sm print:text-xs">
                                <span class="cv-skill-label font-semibold break-words">{{ category.name }}:</span>
                                <span class="text-muted-foreground break-words">{{ category.skills.map((s) => s.name).join(', ') }}</span>
                            </div>
                        </div>
                    </section>

                    <section v-if="settings.show_projects && projects?.length" class="cv-main">
                        <h2 class="mb-3 border-b border-border pb-1 text-sm font-bold tracking-widest uppercase print:border-gray-300">
                            {{ ui.cv?.experience }}
                        </h2>
                        <div class="space-y-5 print:space-y-4">
                            <div v-for="project in projects" :key="project.title">
                                <div class="flex flex-wrap items-baseline justify-between gap-1">
                                    <h3 class="min-w-0 text-[13px] font-semibold break-words">{{ project.title }}</h3>
                                    <span class="text-[10px] text-muted-foreground">{{ project.period }}</span>
                                </div>
                                <p v-if="project.subtitle" class="text-xs text-muted-foreground">{{ project.subtitle }}</p>
                                <HtmlContent
                                    v-if="project.summary || project.solution"
                                    class="mt-1 text-xs text-muted-foreground"
                                    :html="project.summary || project.solution"
                                />
                                <ul v-if="project.highlights?.length" class="mt-1.5 space-y-1">
                                    <li v-for="item in project.highlights" :key="item" class="flex items-start gap-1.5 text-xs text-muted-foreground">
                                        <span>•</span>
                                        <span>{{ item }}</span>
                                    </li>
                                </ul>
                                <div class="mt-1.5 flex flex-wrap gap-1">
                                    <span
                                        v-for="tech in project.tech_stack"
                                        :key="tech"
                                        class="rounded border border-border bg-secondary px-1.5 py-0.5 text-[10px] font-medium print:border-gray-300 print:bg-gray-100"
                                    >
                                        {{ tech }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section v-if="settings.show_languages && languages?.length" class="cv-side">
                        <h2 class="mb-2 border-b border-border pb-1 text-sm font-bold tracking-widest uppercase print:border-gray-300">
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
                        <h2 class="mb-2 border-b border-border pb-1 text-sm font-bold tracking-widest uppercase print:border-gray-300">
                            {{ ui.philosophy?.title }}
                        </h2>
                        <div v-for="item in principles" :key="item.title" class="mb-1 text-sm print:text-xs">
                            <span class="font-semibold">{{ item.title }}</span>
                            <span v-if="item.description" class="text-muted-foreground"> — </span>
                            <HtmlContent v-if="item.description" class="inline text-muted-foreground" :html="item.description" />
                        </div>
                    </section>
                </div>
            </article>
        </div>
    </PublicLayout>
</template>
