<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import CvContactLines from '../../Components/Cv/CvContactLines.vue';
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
    ui: Object,
    display: Object,
});

const page = usePage();
const ui = computed(() => props.ui ?? page.props.ui ?? {});
const showTechStack = computed(() => (props.display ?? {}).show_tech_stack !== false);
const settings = computed(() => props.cv?.settings || {});
const appearance = computed(() => page.props.appearance || {});
const cvLayout = computed(() => appearance.value.cv_layout || settings.value.template || 'modern');
const isSidebarLayout = computed(() => cvLayout.value === 'sidebar');
const isClassicLayout = computed(() => cvLayout.value === 'classic');
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

        <div class="cv-page-wrap min-h-screen bg-muted/30 pb-8 print:bg-white print:pt-0 print:pb-0 print:min-h-0">
            <article
                class="cv-sheet mx-auto w-full max-w-[210mm] overflow-hidden bg-background shadow-xl print:shadow-none print:max-w-none"
                :class="`cv-${cvLayout}`">
                <!-- Sidebar layout: colored left rail + white main column -->
                <div v-if="isSidebarLayout" class="cv-sidebar-layout print:flex-row">
                    <aside class="cv-sidebar-panel">
                        <div class="cv-sidebar-profile text-center">
                            <img v-if="settings.show_avatar && profile?.avatar" :src="profile.avatar"
                                :alt="profile.full_name" loading="lazy"
                                class="mx-auto h-24 w-24 rounded-full border-4 border-white/30 object-cover shadow-md print:h-20 print:w-20" />
                            <h1 class="mt-4 text-xl font-bold leading-tight tracking-tight text-white print:text-lg">
                                {{ profile?.full_name }}
                            </h1>
                            <p v-if="profile?.headline" class="mt-2 text-sm font-medium leading-snug text-white/85">
                                {{ profile.headline }}
                            </p>
                        </div>

                        <div class="cv-sidebar-divider" />

                        <div class="cv-sidebar-block">
                            <CvContactLines :profile="profile" :github="github" inverted />
                        </div>

                        <section v-if="settings.show_education && education?.length" class="cv-sidebar-block">
                            <h2 class="cv-sidebar-heading">{{ ui.cv?.education }}</h2>
                            <div v-for="item in education" :key="item.degree" class="mb-3 last:mb-0">
                                <p class="text-sm font-semibold leading-snug text-white">{{ item.degree }}</p>
                                <p class="mt-0.5 text-xs text-white/75">{{ item.school }}</p>
                                <p class="text-[11px] text-white/60">{{ item.period }}</p>
                                <HtmlContent v-if="item.details" class="cv-sidebar-rich mt-1 text-xs text-white/80"
                                    :html="item.details" />
                            </div>
                        </section>

                        <section v-if="settings.show_skills && skill_categories?.length" class="cv-sidebar-block">
                            <h2 class="cv-sidebar-heading">{{ ui.cv?.skills }}</h2>
                            <div class="space-y-2">
                                <div v-for="category in skill_categories" :key="category.name">
                                    <p class="text-xs font-semibold uppercase tracking-wide text-white/90">{{ category.name }}</p>
                                    <p class="mt-0.5 text-[13px] leading-relaxed text-white/80">
                                        {{ category.skills.map((s) => s.name).join(' · ') }}
                                    </p>
                                </div>
                            </div>
                        </section>

                        <section v-if="settings.show_languages && languages?.length" class="cv-sidebar-block">
                            <h2 class="cv-sidebar-heading">{{ ui.cv?.languages }}</h2>
                            <ul class="space-y-1.5">
                                <li v-for="item in languages" :key="item.name" class="text-[13px] text-white/85">
                                    <span class="font-semibold text-white">{{ item.name }}</span>
                                    <span class="text-white/70"> — {{ item.level }}</span>
                                </li>
                            </ul>
                        </section>

                        <section v-if="settings.show_principles && principles?.length" class="cv-sidebar-block">
                            <h2 class="cv-sidebar-heading">{{ ui.philosophy?.title }}</h2>
                            <div v-for="item in principles" :key="item.title" class="mb-2 last:mb-0 text-[13px] text-white/85">
                                <span class="font-semibold text-white">{{ item.title }}</span>
                                <HtmlContent v-if="item.description" class="cv-sidebar-rich mt-0.5 block text-white/75"
                                    :html="item.description" />
                            </div>
                        </section>
                    </aside>

                    <div class="cv-main-panel">
                        <section v-if="settings.show_about && profile?.about" class="cv-main-block">
                            <h2 class="cv-main-heading">{{ ui.cv?.summary }}</h2>
                            <HtmlContent class="text-sm leading-relaxed text-muted-foreground print:text-gray-700"
                                :html="profile.about" />
                        </section>

                        <section v-if="settings.show_projects && projects?.length" class="cv-main-block">
                            <h2 class="cv-main-heading">{{ ui.cv?.experience }}</h2>
                            <div class="space-y-5 print:space-y-4">
                                <div v-for="project in projects" :key="project.title" class="cv-experience-item">
                                    <div class="flex flex-wrap items-baseline justify-between gap-x-3 gap-y-1">
                                        <h3 class="min-w-0 text-[15px] font-semibold text-foreground">{{ project.title }}</h3>
                                        <span class="shrink-0 text-xs font-medium text-muted-foreground">{{ project.period }}</span>
                                    </div>
                                    <p v-if="project.subtitle" class="mt-0.5 text-xs text-muted-foreground">{{ project.subtitle }}</p>
                                    <HtmlContent v-if="project.summary || project.solution"
                                        class="mt-2 text-sm leading-relaxed text-muted-foreground"
                                        :html="project.summary || project.solution" />
                                    <ul v-if="project.highlights?.length" class="mt-2 list-disc space-y-1 pl-5 text-sm text-muted-foreground">
                                        <li v-for="item in project.highlights" :key="item">{{ item }}</li>
                                    </ul>
                                    <div v-if="showTechStack && project.tech_stack?.length" class="mt-2 flex flex-wrap gap-1.5">
                                        <span v-for="tech in project.tech_stack" :key="tech"
                                            class="rounded-md border border-border bg-muted/50 px-2 py-0.5 text-[10px] font-medium text-foreground print:border-gray-300">
                                            {{ tech }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>

                <!-- Modern / classic: single column -->
                <div v-else
                    class="cv-body text-justify print:p-[15mm]"
                    :class="isClassicLayout ? 'cv-body-classic space-y-6 p-6 sm:p-10 md:p-12' : 'cv-body-modern space-y-8 p-4 sm:p-10 md:p-12'">
                    <!-- ——— Classic ——— -->
                    <template v-if="isClassicLayout">
                        <header class="cv-classic-header">
                            <hr class="cv-classic-rule" />
                            <img v-if="settings.show_avatar && profile?.avatar" :src="profile.avatar"
                                :alt="profile.full_name" loading="lazy"
                                class="cv-classic-avatar mx-auto h-20 w-20 rounded-full object-cover print:h-16 print:w-16" />
                            <h1 class="cv-classic-name">{{ profile?.full_name }}</h1>
                            <p v-if="profile?.headline" class="cv-classic-headline">{{ profile.headline }}</p>
                            <hr class="cv-classic-rule" />
                            <CvContactLines :profile="profile" :github="github" variant="classic-inline" />
                            <hr class="cv-classic-rule cv-classic-rule-strong" />
                        </header>

                        <section v-if="settings.show_about && profile?.about" class="cv-classic-section">
                            <h2 class="cv-classic-section-title">{{ ui.cv?.summary }}</h2>
                            <HtmlContent class="cv-classic-prose" :html="profile.about" />
                        </section>

                        <section v-if="settings.show_education && education?.length" class="cv-classic-section">
                            <h2 class="cv-classic-section-title">{{ ui.cv?.education }}</h2>
                            <div v-for="item in education" :key="item.degree" class="cv-classic-entry">
                                <div class="cv-classic-entry-row">
                                    <span class="font-semibold">{{ item.degree }}</span>
                                    <span class="cv-classic-date">{{ item.period }}</span>
                                </div>
                                <p class="cv-classic-subline">{{ item.school }}</p>
                                <HtmlContent v-if="item.details" class="cv-classic-prose-sm" :html="item.details" />
                            </div>
                        </section>

                        <section v-if="settings.show_skills && skill_categories?.length" class="cv-classic-section">
                            <h2 class="cv-classic-section-title">{{ ui.cv?.skills }}</h2>
                            <p v-for="category in skill_categories" :key="category.name" class="cv-classic-skill-line">
                                <span class="font-semibold">{{ category.name }}:</span>
                                {{ category.skills.map((s) => s.name).join(', ') }}
                            </p>
                        </section>

                        <section v-if="settings.show_projects && projects?.length" class="cv-classic-section">
                            <h2 class="cv-classic-section-title">{{ ui.cv?.experience }}</h2>
                            <div v-for="(project, index) in projects" :key="project.title"
                                :class="['cv-classic-entry', index > 0 ? 'cv-classic-entry-divider' : '']">
                                <div class="cv-classic-entry-row">
                                    <span class="font-semibold">{{ project.title }}</span>
                                    <span class="cv-classic-date">{{ project.period }}</span>
                                </div>
                                <p v-if="project.subtitle" class="cv-classic-subline italic">{{ project.subtitle }}</p>
                                <HtmlContent v-if="project.summary || project.solution" class="cv-classic-prose-sm"
                                    :html="project.summary || project.solution" />
                                <ul v-if="project.highlights?.length" class="cv-classic-list">
                                    <li v-for="item in project.highlights" :key="item">{{ item }}</li>
                                </ul>
                            </div>
                        </section>

                        <section v-if="settings.show_languages && languages?.length" class="cv-classic-section">
                            <h2 class="cv-classic-section-title">{{ ui.cv?.languages }}</h2>
                            <p class="cv-classic-skill-line">
                                <template v-for="(item, index) in languages" :key="item.name">
                                    <span v-if="index > 0"> · </span>
                                    <span class="font-semibold">{{ item.name }}</span> ({{ item.level }})
                                </template>
                            </p>
                        </section>

                        <section v-if="settings.show_principles && principles?.length" class="cv-classic-section">
                            <h2 class="cv-classic-section-title">{{ ui.philosophy?.title }}</h2>
                            <div v-for="item in principles" :key="item.title" class="cv-classic-entry">
                                <p class="font-semibold">{{ item.title }}</p>
                                <HtmlContent v-if="item.description" class="cv-classic-prose-sm" :html="item.description" />
                            </div>
                        </section>
                    </template>

                    <!-- ——— Modern ——— -->
                    <template v-else>
                        <div class="cv-modern-accent" aria-hidden="true" />

                        <header class="cv-modern-header">
                            <div class="flex min-w-0 flex-col gap-5 sm:flex-row sm:items-start sm:justify-between">
                                <div class="flex min-w-0 items-center gap-5">
                                    <img v-if="settings.show_avatar && profile?.avatar" :src="profile.avatar"
                                        :alt="profile.full_name" loading="lazy"
                                        class="h-[4.5rem] w-[4.5rem] shrink-0 rounded-2xl object-cover shadow-sm ring-2 ring-primary/20 sm:h-24 sm:w-24" />
                                    <div class="min-w-0">
                                        <h1 class="text-3xl font-extrabold tracking-tight text-foreground sm:text-4xl print:text-2xl">
                                            {{ profile?.full_name }}
                                        </h1>
                                        <p class="mt-1.5 text-lg font-medium text-primary sm:text-xl print:text-base">
                                            {{ profile?.headline }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <CvContactLines :profile="profile" :github="github" variant="modern-grid" class="mt-5" />
                        </header>

                        <section v-if="settings.show_about && profile?.about" class="cv-modern-section">
                            <h2 class="cv-modern-section-title">{{ ui.cv?.summary }}</h2>
                            <HtmlContent class="text-sm leading-relaxed text-muted-foreground print:text-gray-700"
                                :html="profile.about" />
                        </section>

                        <section v-if="settings.show_education && education?.length" class="cv-modern-section">
                            <h2 class="cv-modern-section-title">{{ ui.cv?.education }}</h2>
                            <div class="space-y-3">
                                <div v-for="item in education" :key="item.degree" class="cv-modern-card">
                                    <div class="flex flex-wrap items-baseline justify-between gap-2">
                                        <h3 class="font-semibold text-foreground">{{ item.degree }}</h3>
                                        <span class="rounded-full bg-primary/10 px-2.5 py-0.5 text-xs font-medium text-primary">
                                            {{ item.period }}
                                        </span>
                                    </div>
                                    <p class="mt-0.5 text-sm text-muted-foreground">{{ item.school }}</p>
                                    <HtmlContent v-if="item.details" class="mt-1 text-sm text-muted-foreground"
                                        :html="item.details" />
                                </div>
                            </div>
                        </section>

                        <section v-if="settings.show_skills && skill_categories?.length" class="cv-modern-section">
                            <h2 class="cv-modern-section-title">{{ ui.cv?.skills }}</h2>
                            <div class="space-y-3">
                                <div v-for="category in skill_categories" :key="category.name">
                                    <p class="mb-1.5 text-xs font-bold uppercase tracking-wider text-primary">
                                        {{ category.name }}
                                    </p>
                                    <div class="flex flex-wrap gap-1.5">
                                        <span v-for="skill in category.skills" :key="skill.name"
                                            class="rounded-md bg-muted px-2 py-1 text-xs font-medium text-foreground print:border print:border-gray-300">
                                            {{ skill.name }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <section v-if="settings.show_projects && projects?.length" class="cv-modern-section">
                            <h2 class="cv-modern-section-title">{{ ui.cv?.experience }}</h2>
                            <div class="cv-modern-timeline">
                                <div v-for="project in projects" :key="project.title" class="cv-modern-timeline-item">
                                    <div class="flex flex-wrap items-baseline justify-between gap-2">
                                        <h3 class="text-base font-semibold text-foreground">{{ project.title }}</h3>
                                        <span class="text-xs font-medium text-muted-foreground">{{ project.period }}</span>
                                    </div>
                                    <p v-if="project.subtitle" class="text-sm text-primary/80">{{ project.subtitle }}</p>
                                    <HtmlContent v-if="project.summary || project.solution"
                                        class="mt-2 text-sm leading-relaxed text-muted-foreground"
                                        :html="project.summary || project.solution" />
                                    <ul v-if="project.highlights?.length" class="mt-2 space-y-1">
                                        <li v-for="item in project.highlights" :key="item"
                                            class="flex items-start gap-2 text-sm text-muted-foreground">
                                            <span class="mt-2 h-1.5 w-1.5 shrink-0 rounded-full bg-primary" />
                                            {{ item }}
                                        </li>
                                    </ul>
                                    <div v-if="showTechStack && project.tech_stack?.length" class="mt-2 flex flex-wrap gap-1.5">
                                        <span v-for="tech in project.tech_stack" :key="tech"
                                            class="rounded border border-primary/25 bg-primary/5 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-primary">
                                            {{ tech }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <section v-if="settings.show_languages && languages?.length" class="cv-modern-section">
                            <h2 class="cv-modern-section-title">{{ ui.cv?.languages }}</h2>
                            <div class="grid gap-2 sm:grid-cols-2">
                                <div v-for="item in languages" :key="item.name"
                                    class="flex items-center justify-between rounded-lg border border-border bg-muted/30 px-3 py-2 text-sm">
                                    <span class="font-semibold">{{ item.name }}</span>
                                    <span class="text-muted-foreground">{{ item.level }}</span>
                                </div>
                            </div>
                        </section>

                        <section v-if="settings.show_principles && principles?.length" class="cv-modern-section">
                            <h2 class="cv-modern-section-title">{{ ui.philosophy?.title }}</h2>
                            <div class="grid gap-3 sm:grid-cols-2">
                                <div v-for="item in principles" :key="item.title"
                                    class="rounded-lg border-l-4 border-primary bg-muted/25 px-3 py-2">
                                    <p class="font-semibold text-foreground">{{ item.title }}</p>
                                    <HtmlContent v-if="item.description" class="mt-1 text-sm text-muted-foreground"
                                        :html="item.description" />
                                </div>
                            </div>
                        </section>
                    </template>
                </div>
            </article>
        </div>
    </PublicLayout>
</template>
