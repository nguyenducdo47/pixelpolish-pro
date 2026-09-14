<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import Card3D from '../../Components/Card3D.vue';
import HtmlContent from '../../Components/HtmlContent.vue';
import LocaleSelect from '../../Components/LocaleSelect.vue';
import ParticleBackground from '../../Components/ParticleBackground.vue';
import SocialIcon from '../../Components/SocialIcon.vue';
import PublicLayout from '../../Layouts/PublicLayout.vue';
import { useTypingLines } from '../../composables/useTypingLines';
import { techMeta } from '../../lib/techLogos';

const props = defineProps({
    locale: String,
    username: String,
    profile: Object,
    social_links: Array,
    skill_categories: Array,
    projects: Array,
    education: Array,
    languages: Array,
    principles: Array,
    seo: Object,
    cv: Object,
    available_locales: Array,
    ui: Object,
    display: Object,
});

const page = usePage();
const ui = computed(() => props.ui ?? page.props.ui ?? {});
const display = computed(() => props.display ?? {});
const skillDisplay = computed(() => display.value.skill_display ?? 'percent');
const showTechLogos = computed(() => display.value.show_tech_logos !== false);
const showTechStack = computed(() => display.value.show_tech_stack !== false);

function showsProjectField(field) {
    const fields = display.value.project_fields;
    if (!fields || typeof fields !== 'object') {
        return true;
    }

    return fields[field] !== false;
}

function sectionEnabled(section) {
    const sections = display.value.sections;
    if (!sections || typeof sections !== 'object') {
        return true;
    }

    return sections[section] !== false;
}
const appearance = computed(() => page.props.appearance || {});
const showParticles = computed(
    () => appearance.value.show_particles !== false && appearance.value.hero !== 'minimal',
);
const showGlow = computed(() => appearance.value.hero !== 'minimal');
const { displayed, currentLine, done } = useTypingLines(() => [
    props.profile?.full_name,
    props.profile?.tagline,
]);

function firstName(name) {
    return String(name || '').trim().split(/\s+/)[0] || 'Dev';
}
</script>

<template>
    <PublicLayout>
        <template #brand>
            <a href="#top" class="font-heading">
                <span class="text-gradient">{{ firstName(profile?.full_name) }}</span>
            </a>
        </template>
        <template #nav>
            <a href="#about" class="hover:text-foreground">{{ ui.nav?.about }}</a>
            <a v-if="sectionEnabled('skills')" href="#skills" class="hover:text-foreground">{{ ui.nav?.skills }}</a>
            <a v-if="sectionEnabled('projects')" href="#projects" class="hover:text-foreground">{{ ui.nav?.projects }}</a>
            <a v-if="sectionEnabled('philosophy')" href="#philosophy" class="hover:text-foreground">{{ ui.nav?.philosophy }}</a>
            <a href="#contact" class="hover:text-foreground">{{ ui.nav?.contact }}</a>
            <Link :href="cv.url" class="text-primary hover:opacity-80">{{ ui.nav?.cv }}</Link>
        </template>
        <template #locale>
            <LocaleSelect
                :model-value="locale"
                :options="(available_locales || []).map((item) => ({ ...item, href: item.portfolio_url }))"
            />
        </template>
        <template #footer>
            <p>© {{ new Date().getFullYear() }} {{ profile?.full_name }}</p>
            <p>{{ ui.footer?.designed || 'Designed by' }} <span class="text-primary">{{ firstName(profile?.full_name) }}</span></p>
        </template>

        <Head :title="seo?.title">
            <meta name="description" :content="seo?.description" />
        </Head>

        <section id="top" class="relative flex min-h-[calc(100svh-5rem)] flex-col items-center justify-center overflow-x-clip px-4 py-16 sm:py-20">
            <ParticleBackground v-if="showParticles" />
            <div class="absolute inset-0 bg-gradient-hero" />
            <div
                v-if="showGlow"
                class="animate-glow-pulse pointer-events-none absolute top-1/4 left-1/2 h-[240px] w-[240px] -translate-x-1/2 rounded-full bg-primary/5 blur-3xl sm:h-[520px] sm:w-[520px]"
            />
            <div class="section-container relative z-10 max-w-full text-center">
                <span
                    v-if="profile?.headline"
                    class="mb-6 inline-block max-w-full break-words rounded-full border border-border bg-secondary px-4 py-1.5 text-xs text-muted-foreground sm:text-sm"
                >
                    {{ profile.headline }}
                </span>
                <h1 class="hero-title mb-6 text-3xl font-bold leading-snug sm:text-5xl md:text-6xl lg:text-7xl">
                    <span class="block max-w-full break-words">{{ displayed[0] }}<span v-if="currentLine === 0" class="animate-pulse">|</span></span>
                    <span v-if="displayed[1]" class="mt-3 block max-w-full break-words text-gradient">
                        {{ displayed[1] }}<span v-if="currentLine === 1" class="animate-pulse">|</span>
                    </span>
                </h1>
                <div v-show="done" class="flex flex-col justify-center gap-3 sm:flex-row sm:flex-wrap">
                    <Link :href="cv.url" class="btn-primary">{{ ui.hero?.cta_cv }}</Link>
                    <a href="#contact" class="btn-outline">{{ ui.hero?.cta_contact }}</a>
                </div>
            </div>
            <a href="#about" class="relative z-10 mt-10 text-muted-foreground hover:text-primary">
                <span class="mb-1 block text-xs sm:text-sm">{{ ui.hero?.scroll_more }}</span>
                <span class="block animate-bounce text-center">↓</span>
            </a>
        </section>

        <section id="about" class="relative py-16 sm:py-20 md:py-24">
            <div class="section-container">
                <div class="mb-12 text-center">
                    <h2 class="section-title"><span class="text-gradient">{{ ui.about?.title }}</span></h2>
                </div>
                <div class="space-y-8">
                    <div class="text-justify">
                        <HtmlContent v-if="profile?.about" class="text-base leading-relaxed text-foreground/90 sm:text-lg" :html="profile.about" />
                        <p v-else class="text-muted-foreground">{{ ui.empty }}</p>
                    </div>
                    <div v-if="profile?.avatar" class="flex justify-center">
                        <img
                            :src="profile.avatar"
                            :alt="profile.full_name"
                            loading="lazy"
                            class="h-40 w-40 rounded-2xl border border-border object-cover shadow-card sm:h-48 sm:w-48"
                        />
                    </div>
                    <div v-if="education?.length" class="space-y-3">
                        <article
                            v-for="item in education"
                            :key="item.degree"
                            class="flex gap-4 rounded-xl border border-border bg-card p-5 transition-colors hover:border-primary/30"
                        >
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-primary/10 text-primary">
                                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path d="M4 10.5 12 6l8 4.5L12 15 4 10.5Z" />
                                    <path d="M7 12.2v4.3c0 .4 2.2 1.5 5 1.5s5-1.1 5-1.5v-4.3" />
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <h3 class="break-words font-semibold">{{ item.degree }}</h3>
                                <p class="text-sm text-muted-foreground">{{ item.school }} · {{ item.period }}</p>
                                <HtmlContent v-if="item.details" class="mt-2 text-sm text-muted-foreground" :html="item.details" />
                            </div>
                        </article>
                    </div>
                </div>
            </div>
        </section>

        <section id="skills" class="bg-card/50 py-16 sm:py-20 md:py-24" v-if="sectionEnabled('skills') && skill_categories?.length">
            <div class="section-container">
                <div class="mb-12 text-center">
                    <h2 class="section-title"><span class="text-gradient">{{ ui.skills?.title }}</span></h2>
                </div>
                <div class="space-y-6">
                    <article
                        v-for="category in skill_categories"
                        :key="category.name"
                        class="rounded-2xl border border-border bg-card p-6 transition-all hover:border-primary/30"
                    >
                        <h3 class="mb-6 text-xl font-semibold text-primary">{{ category.name }}</h3>
                        <div class="space-y-5">
                            <div v-for="skill in category.skills" :key="skill.name">
                                <template v-if="skillDisplay === 'percent'">
                                    <div class="mb-2 flex items-center gap-3">
                                        <div
                                            v-if="showTechLogos"
                                            class="flex h-10 w-10 items-center justify-center rounded-lg bg-background p-1.5"
                                            :style="{ boxShadow: `0 0 12px ${techMeta(skill.name).color}30` }"
                                        >
                                            <img
                                                v-if="techMeta(skill.name).src"
                                                :src="techMeta(skill.name).src"
                                                :alt="skill.name"
                                                loading="lazy"
                                                class="h-full w-full object-contain"
                                            />
                                            <span v-else class="text-xs font-bold" :style="{ color: techMeta(skill.name).color }">
                                                {{ skill.name.charAt(0) }}
                                            </span>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <div class="flex items-center justify-between gap-2">
                                                <h4 class="truncate font-medium">{{ skill.name }}</h4>
                                                <span class="shrink-0 text-sm font-semibold text-primary">{{ skill.level }}%</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="h-2 overflow-hidden rounded-full bg-muted" :class="showTechLogos ? 'ml-[3.25rem]' : ''">
                                        <div
                                            class="h-full rounded-full"
                                            :style="{
                                                width: `${skill.level}%`,
                                                background: showTechLogos
                                                    ? `linear-gradient(90deg, ${techMeta(skill.name).color}, ${techMeta(skill.name).color}99)`
                                                    : undefined,
                                            }"
                                            :class="!showTechLogos ? 'bg-primary' : ''"
                                        />
                                    </div>
                                </template>
                                <div v-else class="rounded-lg border border-border/60 bg-background/50 px-4 py-3">
                                    <h4 class="font-medium">{{ skill.name }}</h4>
                                    <HtmlContent
                                        v-if="skill.description"
                                        class="mt-1 text-sm text-muted-foreground"
                                        :html="skill.description"
                                    />
                                </div>
                            </div>
                        </div>
                    </article>
                </div>
            </div>
        </section>

        <section id="projects" class="py-16 sm:py-20 md:py-24" v-if="sectionEnabled('projects') && projects?.length">
            <div class="section-container">
                <div class="mb-12 text-center">
                    <h2 class="section-title"><span class="text-gradient">{{ ui.projects?.title }}</span></h2>
                </div>
                <div class="space-y-6">
                    <Card3D v-for="project in projects" :key="project.title">
                        <article class="group overflow-hidden rounded-2xl border border-border/50 bg-card/80 p-6 backdrop-blur-sm transition-all hover:border-primary/50 hover:bg-card md:p-8">
                            <div class="mb-4 flex flex-wrap items-start justify-between gap-3">
                                <div class="min-w-0 max-w-full">
                                    <h3 class="break-words text-xl font-semibold group-hover:text-primary">{{ project.title }}</h3>
                                    <p class="mt-0.5 text-sm text-muted-foreground">{{ project.subtitle }}</p>
                                </div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <span v-if="project.complexity && showsProjectField('complexity')" class="rounded-full bg-secondary px-3 py-1 text-xs font-medium text-secondary-foreground">
                                        {{ project.complexity }}
                                    </span>
                                    <span v-if="project.period" class="text-xs text-muted-foreground">{{ project.period }}</span>
                                    <a
                                        v-if="project.demo_url"
                                        :href="project.demo_url"
                                        target="_blank"
                                        class="text-xs text-primary hover:underline"
                                    >
                                        {{ project.demo_label || project.demo_url }}
                                    </a>
                                </div>
                            </div>
                            <div v-if="showTechStack && project.tech_stack?.length" class="mb-4 flex flex-wrap gap-2">
                                <span
                                    v-for="tech in project.tech_stack"
                                    :key="tech"
                                    class="rounded-md bg-muted px-2 py-1 text-xs text-muted-foreground group-hover:bg-primary/10 group-hover:text-primary"
                                >
                                    {{ tech }}
                                </span>
                            </div>
                            <div v-if="project.problem && showsProjectField('problem')" class="mb-3">
                                <h4 class="mb-1 text-sm font-semibold text-primary">{{ ui.projects?.problem }}</h4>
                                <HtmlContent class="text-sm text-foreground/80" :html="project.problem" />
                            </div>
                            <div v-if="project.solution && showsProjectField('solution')" class="mb-3">
                                <h4 class="mb-1 text-sm font-semibold text-primary">{{ ui.projects?.solution }}</h4>
                                <HtmlContent class="text-sm text-foreground/80" :html="project.solution" />
                            </div>
                            <ul v-if="project.highlights?.length" class="mb-4 space-y-1.5">
                                <li class="mb-1 text-sm font-semibold text-primary">{{ ui.projects?.responsibilities }}</li>
                                <li v-for="item in project.highlights" :key="item" class="flex items-start gap-2 text-sm text-foreground/80">
                                    <span class="mt-2 h-1.5 w-1.5 shrink-0 rounded-full bg-primary" />
                                    <span>{{ item }}</span>
                                </li>
                            </ul>
                            <div v-if="project.learned && showsProjectField('learned')" class="border-t border-border pt-3">
                                <h4 class="mb-1 text-sm font-semibold text-primary">{{ ui.projects?.learned }}</h4>
                                <HtmlContent class="text-sm italic text-muted-foreground" :html="project.learned" />
                            </div>
                            <div v-if="project.github_url && showsProjectField('github_url')" class="mt-4 text-sm">
                                <a :href="project.github_url" target="_blank" class="text-muted-foreground hover:text-primary">GitHub</a>
                            </div>
                        </article>
                    </Card3D>
                </div>
            </div>
        </section>

        <section id="philosophy" class="bg-card/50 py-16 sm:py-20 md:py-24" v-if="sectionEnabled('philosophy') && (principles?.length || profile?.philosophy_quote)">
            <div class="section-container">
                <div class="mb-12 text-center">
                    <h2 class="section-title"><span class="text-gradient">{{ ui.philosophy?.title }}</span></h2>
                </div>
                <div
                    v-if="profile?.philosophy_quote"
                    class="mb-8 rounded-2xl border border-border bg-card p-6"
                >
                    <HtmlContent class="text-lg text-foreground/90" :html="profile.philosophy_quote" />
                </div>
                <div class="space-y-4">
                    <article
                        v-for="item in principles"
                        :key="item.title"
                        class="rounded-xl border border-border bg-card p-5 transition-all hover:border-primary/30"
                    >
                        <h3 class="mb-2 break-words font-semibold">{{ item.title }}</h3>
                        <HtmlContent v-if="item.description" class="text-sm text-muted-foreground" :html="item.description" />
                    </article>
                </div>
            </div>
        </section>

        <section id="contact" class="relative overflow-hidden py-16 sm:py-20 md:py-24">
            <div class="absolute inset-0 bg-gradient-hero opacity-50" />
            <div class="absolute top-1/2 left-1/2 h-[300px] w-[600px] -translate-x-1/2 -translate-y-1/2 rounded-full bg-primary/5 blur-3xl" />
            <div class="section-container relative z-10 mx-auto max-w-3xl text-center">
                <h2 class="section-title"><span class="text-gradient">{{ ui.contact?.title }}</span></h2>
                <div class="mb-10 flex flex-col justify-center gap-3 sm:flex-row sm:flex-wrap">
                    <Link :href="cv.url" class="btn-primary">{{ ui.contact?.view_cv }}</Link>
                    <a v-if="profile?.email" :href="`mailto:${profile.email}`" class="btn-outline break-all">{{ profile.email }}</a>
                    <a :href="cv.pdf_url" class="btn-outline">{{ ui.contact?.download_cv }}</a>
                </div>
                <div class="flex flex-wrap justify-center gap-3">
                    <a
                        v-for="link in social_links"
                        :key="link.url"
                        :href="link.url"
                        target="_blank"
                        :title="link.platform"
                        class="flex h-12 w-12 items-center justify-center rounded-xl border border-border bg-secondary text-muted-foreground transition-all hover:border-primary/50 hover:text-primary"
                    >
                        <SocialIcon :platform="link.platform" :url="link.url" />
                    </a>
                    <a
                        v-if="profile?.email"
                        :href="`mailto:${profile.email}`"
                        class="flex h-12 w-12 items-center justify-center rounded-xl border border-border bg-secondary text-muted-foreground transition-all hover:border-primary/50 hover:text-primary"
                    >
                        <SocialIcon platform="mail" :url="`mailto:${profile.email}`" />
                    </a>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
