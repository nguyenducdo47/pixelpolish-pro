# Data Flow

## Content: DB → public page

```
Portfolio section models (JSON locale fields)
    → PortfolioPresenter::publicPayload($portfolio, $locale)
        → localeText / localeHtml / localeList (HasLocaleText + RichText)
    → Inertia props on Show.vue / Cv.vue
```

**Single builder for public data:** `PortfolioPresenter`. CV PDF reuses the same array via `PortfolioController::payload()`.

## Content: Studio → DB

Two parallel edit surfaces (same tables):

| Path | Mechanism |
|------|-----------|
| Setup wizard | `SetupWizard` → `PortfolioWizardSync::saveAll` (step validation persists incrementally) |
| Nav resources / pages | Filament CRUD on individual models or `ManageProfile`, `ManageCv`, `ManageAppearance` |

Wizard sync uses repeater `id` fields + delete rows missing from payload (`sync()` / `saveSkills()`).

## Appearance → public CSS

```
themes (catalog defaults)
    + portfolios.theme_id
    + portfolio_themes.customization (diff only)
        → AppearanceTheme::resolve()
        → AppearanceTheme::public() (+ css / dark_css)
        → Inertia `appearance` + PublicLayout injects variables
```

Changing CV template from **Appearance** or **Manage CV** both funnel through `AppearanceTheme::syncCvLayout` or `apply()` updating `cv_settings.template`.

## Registration bootstrap

```
RegisterRequest → User created
    → UserObserver::created
        → Portfolio (slug = username, unpublished)
        → Profile (empty locale bags)
        → CvSetting (defaults)
```

## Locale enablement → routing

```
locales table (is_enabled)
    → LocaleCatalog::isEnabled
    → SetLocale middleware 404 if URL locale disabled
    → available_locales links in presenter
```

## Translation (authoring aid)

Rich editor modal → `TextTranslator` → `TranslationApi::active()` ordered list → HTTP drivers (failover).

Does **not** auto-write other locale fields; author inserts translated text manually.

## Cache

`LocaleCatalog` static memo — cleared on `Locale` model save/delete and seeders (`LocaleCatalog::forget()`).

Admin/studio header **Clear cache** → `optimize:clear`.

## Avatar (wizard preview)

```
FileUpload (storeFiles: false, temp Livewire file)
    → WizardPendingAvatar::remember (session)
    → PortfolioPresenter uses urlFor() for owner/admin preview
    → commit on wizard finish → Profile::storedAvatarPath → public disk avatars/
```

Public visitors only see committed `profiles.avatar_path`.
