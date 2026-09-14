# Content profiles (multi-industry)

Portfolios can target different professions without separate products. A **content profile** on `portfolios.content_profile` drives labels, visible form fields, section visibility, and public rendering.

## Values

| Key | Audience | Skills UI | Project extras | Philosophy on site |
|-----|----------|-----------|----------------|---------------------|
| `it` (default for existing rows) | Developers | Percent bars + tech logos | Full IT fields | Yes |
| `general` | Any profession | Name + description | summary, highlights, demo | Yes |
| `creative` | Design / media | List | tags, demo | Yes |
| `education` | Teachers / trainers | List | summary, highlights, **learned** | Yes |
| `business` | Consulting / business | List | problem, solution, highlights | **No** (section off) |

New accounts get `general` from `UserObserver`. Existing portfolios stay `it` after migration unless changed in Studio.

## Config (Phase E)

All rules live in **`config/content_profiles.php`** (sections, fields, suggested theme slug, demo slug per profile). PHP merges this with translations — do not duplicate field rules in Vue.

| Config key | Purpose |
|------------|---------|
| `profiles.{key}.sections` | Hide skills/projects/philosophy on public site + CV toggles |
| `profiles.{key}.project_fields` | Filament / wizard project form |
| `profiles.{key}.suggested_theme` | Applied when portfolio has no `theme_id` (registration, profile save) |
| `demo_slugs.{key}` | Landing page demo link per profile tab |

## Source of truth

| Concern | File |
|---------|------|
| Enum | `app/Enums/ContentProfile.php` |
| Rules + public `display` payload | `app/Support/ContentProfileConfig.php` |
| Landing profile tabs | `app/Support/ContentProfileLanding.php`, `PortfolioController::landing` |
| Public labels | `lang/{locale}/content_profiles.php` |
| Panel nav / wizard / fields | `lang/{locale}/panel.php` → `content_profile_*` |
| Presenter | `app/Services/PortfolioPresenter.php` |
| Studio nav labels | `ContentProfileConfig::resolvePanelNavLabel()` via `TranslatesNavigation` |
| Project forms | `app/Filament/Support/ProjectFormSchema.php` |

## Editing

Studio → **Profile**: **Industry focus** (`content_profile`). Studio sidebar labels for Projects/Skills follow the profile. Wizard step titles for skills/projects follow the profile when `content_profile` is set on step 1.

Suggested theme is applied automatically only while `theme_id` is still empty (new users). Change theme anytime under **Appearance**.

## Landing (Phase D)

`/` accepts `?profile=general|it|creative|education|business` to preview marketing copy and demo URL per vertical. Register/login links preserve `?profile=` when present.

Demo slugs are configured in `config/content_profiles.php` → `demo_slugs` (seeded by `DemoPortfolioSeeder` + `VerticalDemoSeeder`).

## Studio alignment (Phase F)

| Behavior | Implementation |
|----------|----------------|
| Hide sidebar items when section off | `RespectsContentProfileSection` on Skills, Projects, Philosophy resources (+ skill items) |
| CV toggles match sections | `ManageCv`, wizard CV step — labels via `panelNavLabel`, toggles hidden when section off |
| Wizard presence | Principles repeater hidden when philosophy off |
| CV DB flags | `syncCvSettingsForSections()` on profile save (wizard + Manage profile) |
| Getting started guide | `PortfolioGuide` skips disabled sections; step titles use profile nav labels |
| Dashboard widget | Shows current industry focus label |

Trait: `app/Filament/Concerns/RespectsContentProfileSection.php` — set `$contentProfileSection` on each resource.

## Onboarding & demos (Phase G)

| Feature | Implementation |
|---------|----------------|
| Per-vertical demo sites | `VerticalDemoSeeder`, `ContentProfileDemoSamples`, `demo_slugs` in config |
| Wizard step order | `wizard_step_order` (global) or `wizard_steps` per profile in config; `SetupWizard::wizardSteps()` |
| Register → studio | `Studio::home($profile)`, flash welcome on setup, CV flags synced on register |
| Register UI | Shows selected industry; `register_profile_prefix` in `lang/*/ui.php` |

Seed demos (user runs locally):

```bash
php artisan db:seed --class=VerticalDemoSeeder
# or full: php artisan db:seed
```

Demo accounts use password `password` (e.g. `demo-general@portfotilo.local` → `/vi/demo-general`).

## Extending

1. Add enum case + block in `config/content_profiles.php`.
2. Add `lang/*/content_profiles.php` and optional `panel.content_profile_*` keys.
3. No migration unless new stored fields are required.
