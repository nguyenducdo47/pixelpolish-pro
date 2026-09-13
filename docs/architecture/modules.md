# Modules

## `app/Http/Controllers`

| Controller | Responsibility |
|------------|----------------|
| `PortfolioController` | Landing, show, CV, PDF; publish gate; PDF avatar base64 |
| Auth controllers | Login, register, forgot/reset password |
| `ImpersonationController` | Admin session swap |
| `ClearCacheController` | `optimize:clear` + Filament component cache |
| `WizardAvatarController` | Serve pending Livewire temp avatar for preview |

## `app/Services`

| Service | Source of truth for |
|---------|---------------------|
| `PortfolioPresenter` | **Public API shape** — all data sent to Inertia/CV/PDF |
| `PortfolioWizardSync` | **Wizard bulk save** — profile, repeaters, CV toggles, sync |
| `TextTranslator` | External translation HTTP calls |

## `app/Support`

| Class | Role |
|-------|------|
| `AppearanceTheme` | Theme resolve/apply/import/export; CSS vars for public |
| `LocaleCatalog` | Cached DB locales; enabled codes; default |
| `HasLocaleText` | Model trait: pick locale from JSON bags |
| `LocaleTabs` | Filament multi-locale form UX |
| `RichText` | Editor state → stored HTML string |
| `UiLocale` | Panel UI language |
| `WizardPendingAvatar` | Session-staged avatar before commit |
| `Studio` | Studio home URL constant |

## `app/Filament`

- **Pages**: `SetupWizard` (primary onboarding), `ManageProfile`, `ManageAppearance`, `ManageCv`, `ManageMail` (admin), `Dashboard`
- **Resources**: CRUD for portfolio sections; admin-only: `Users`, `Locales`, `Themes`, `TranslationApis`
- **Forms**: `LocaleTabs`, `FullRichEditor` (+ translate plugin), `ThemeAppearanceFields`

Both panels **discover the same** `Filament/Resources` and `Filament/Pages`; visibility is gated per class (`canViewAny`, `canAccess`, panel id checks).

## `app/Models`

Core: `User`, `Portfolio`, `Profile`, section models (`Project`, `Skill`, …), `CvSetting`, `Theme`, `PortfolioTheme`, `Locale`, `TranslationApi`, `MailSetting`.

**Global scopes (Filament only):**

- `Portfolio`: `current_user` → `user_id = auth id`
- `BelongsToPortfolio` models: `current_portfolio` → current user's `portfolio_id`

Use `withoutGlobalScopes()` in migrations/seeders when iterating all portfolios.

## `resources/js`

| Path | Role |
|------|------|
| `Pages/Landing.vue` | Home |
| `Pages/Portfolio/Show.vue` | Public portfolio |
| `Pages/Portfolio/Cv.vue` | Web CV |
| `Layouts/PublicLayout.vue` | Applies `appearance` CSS variables + dark mode from `theme` |
| `Pages/Auth/*` | Login/register |

## `resources/views/cv`

Blade templates for PDF (`pdf.blade.php`) — same payload array as Inertia, not a separate data builder.

## `lang/{locale}/`

- `ui.php` — public chrome strings (shared via Inertia)
- `panel.php` — Filament translations

## Tests (behavior anchors)

`tests/Feature/AuthTest.php`, `TranslationApiTest.php`, `TextTranslatorTest.php`, `LivewireTempUploadTest.php`
