<?php

namespace App\Support;

use App\Enums\ContentProfile;
use App\Models\Portfolio;
use App\Models\Theme;
use Filament\Facades\Filament;

class ContentProfileConfig
{
    /**
     * @param  array<string, mixed>  $definition
     */
    public function __construct(
        public readonly ContentProfile $profile,
        private readonly array $definition,
    ) {}

    public static function for(?Portfolio $portfolio): self
    {
        $profile = $portfolio?->content_profile ?? ContentProfile::It;

        if (! $profile instanceof ContentProfile) {
            $profile = ContentProfile::tryFrom((string) $profile) ?? ContentProfile::It;
        }

        return self::forProfile($profile);
    }

    public static function forValue(mixed $value): self
    {
        if ($value instanceof ContentProfile) {
            return self::forProfile($value);
        }

        return self::forProfile(ContentProfile::tryFrom((string) $value) ?? ContentProfile::It);
    }

    public static function forProfile(ContentProfile $profile): self
    {
        $definitions = config('content_profiles.profiles', []);
        $definition = $definitions[$profile->value] ?? $definitions[ContentProfile::It->value] ?? [];

        return new self($profile, $definition);
    }

    public function usesSkillPercent(): bool
    {
        return ($this->definition['skill_display'] ?? 'percent') === 'percent';
    }

    public function showTechLogos(): bool
    {
        return (bool) ($this->definition['show_tech_logos'] ?? true);
    }

    public function showsProjectField(string $field): bool
    {
        $fields = $this->definition['project_fields'] ?? [];

        return (bool) ($fields[$field] ?? false);
    }

    public function sectionEnabled(string $section): bool
    {
        $sections = $this->definition['sections'] ?? [];

        return (bool) ($sections[$section] ?? true);
    }

    /**
     * @return array<string, bool>
     */
    public function enabledSections(): array
    {
        $sections = $this->definition['sections'] ?? [];

        return [
            'skills' => (bool) ($sections['skills'] ?? true),
            'projects' => (bool) ($sections['projects'] ?? true),
            'philosophy' => (bool) ($sections['philosophy'] ?? true),
        ];
    }

    public function richCvProjects(): bool
    {
        return (bool) ($this->definition['rich_cv_projects'] ?? true);
    }

    public function suggestedThemeSlug(): ?string
    {
        $slug = $this->definition['suggested_theme'] ?? null;

        return is_string($slug) && $slug !== '' ? $slug : null;
    }

    public function demoSlug(): string
    {
        $slugs = config('content_profiles.demo_slugs', []);
        $slug = $slugs[$this->profile->value] ?? null;

        if (is_string($slug) && $slug !== '') {
            return $slug;
        }

        return (string) config('content_profiles.default_demo_slug', 'nguyenducdo');
    }

    /**
     * @return list<string>
     */
    public function wizardStepOrder(): array
    {
        $custom = $this->definition['wizard_steps'] ?? null;

        if (is_array($custom) && $custom !== []) {
            return array_values($custom);
        }

        $global = config('content_profiles.wizard_step_order');

        if (is_array($global) && $global !== []) {
            return array_values($global);
        }

        return ['profile', 'skills', 'projects', 'background', 'presence', 'cv', 'preview'];
    }

    public function applySuggestedThemeIfUnset(Portfolio $portfolio): void
    {
        if ($portfolio->theme_id) {
            return;
        }

        $slug = $this->suggestedThemeSlug();

        if (! $slug) {
            return;
        }

        $theme = Theme::query()->where('slug', $slug)->where('is_enabled', true)->first();

        if (! $theme) {
            return;
        }

        AppearanceTheme::apply($portfolio, ['theme_id' => $theme->id]);
    }

    /**
     * @return array<string, mixed>
     */
    public function toPublicArray(): array
    {
        return [
            'key' => $this->profile->value,
            'skill_display' => $this->definition['skill_display'] ?? 'percent',
            'show_tech_logos' => $this->showTechLogos(),
            'show_tech_stack' => $this->showsProjectField('tech_stack'),
            'rich_cv_projects' => $this->richCvProjects(),
            'project_fields' => $this->definition['project_fields'] ?? [],
            'sections' => $this->enabledSections(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function mergedUi(string $locale): array
    {
        $base = trans('ui', [], $locale);
        $overrides = trans('content_profiles.'.$this->profile->value, [], $locale);

        if (! is_array($overrides)) {
            return $base;
        }

        unset($overrides['landing']);

        return array_replace_recursive($base, $overrides);
    }

    /**
     * @return array<string, string>
     */
    public function landingCopy(string $locale): array
    {
        $profileLanding = trans('content_profiles.'.$this->profile->value.'.landing', [], $locale);
        $base = trans('ui.landing', [], $locale);

        if (! is_array($profileLanding)) {
            return is_array($base) ? $base : [];
        }

        return array_replace(is_array($base) ? $base : [], $profileLanding);
    }

    public function panelFieldLabel(string $field): string
    {
        $key = 'panel.content_profile_fields.'.$this->profile->value.'.'.$field;
        $translated = __($key);

        if ($translated !== $key) {
            return $translated;
        }

        return __('panel.fields.'.$field);
    }

    public function syncCvSettingsForSections(Portfolio $portfolio): void
    {
        $cv = $portfolio->cvSettings;

        if (! $cv) {
            return;
        }

        $updates = [];

        if (! $this->sectionEnabled('skills')) {
            $updates['show_skills'] = false;
        }

        if (! $this->sectionEnabled('projects')) {
            $updates['show_projects'] = false;
        }

        if (! $this->sectionEnabled('philosophy')) {
            $updates['show_principles'] = false;
        }

        if ($updates !== []) {
            $cv->update($updates);
        }
    }

    public function panelNavLabel(string $navKey): string
    {
        $key = 'panel.content_profile_nav.'.$this->profile->value.'.'.$navKey;
        $translated = __($key);

        if ($translated !== $key) {
            return $translated;
        }

        return __('panel.nav.'.$navKey);
    }

    public function wizardLabel(string $step): string
    {
        $key = 'panel.content_profile_wizard.'.$this->profile->value.'.'.$step;
        $translated = __($key);

        if ($translated !== $key) {
            return $translated;
        }

        return __('panel.wizard.'.$step);
    }

    public function wizardDescription(string $step): string
    {
        $key = 'panel.content_profile_wizard.'.$this->profile->value.'.'.$step.'_desc';
        $translated = __($key);

        if ($translated !== $key) {
            return $translated;
        }

        return __('panel.wizard.'.$step.'_desc');
    }

    public static function resolvePanelNavLabel(string $panelNavTranslationKey): string
    {
        if (! self::shouldTranslateForPortfolio()) {
            return __($panelNavTranslationKey);
        }

        $navKey = str_contains($panelNavTranslationKey, '.')
            ? (string) str($panelNavTranslationKey)->afterLast('.')
            : $panelNavTranslationKey;

        return self::for(auth()->user()?->portfolio)->panelNavLabel($navKey);
    }

    protected static function shouldTranslateForPortfolio(): bool
    {
        if (! auth()->check()) {
            return false;
        }

        $panel = Filament::getCurrentPanel();

        return $panel !== null && $panel->getId() === 'studio';
    }
}
