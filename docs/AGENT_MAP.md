# Agent Map

## Purpose

Navigation index for AI agents working on **Portfotilo** (Laravel + Inertia/Vue + Filament portfolio builder). Use this file to pick the right **business doc**, **source of truth**, and **files to open**—without scanning the whole repository.

Detailed rules live in `docs/business/*`, `docs/workflows/*`, and `docs/AGENT_GUIDE.md`. This map does not replace them.

---

# 1. How Agent Should Use This File

1. Parse the user task → identify **domain(s)** (§3) and **task type** (§4).
2. Read **Documentation Priority** (§12)—start with this map, then linked business/workflow docs.
3. Open **source of truth** for that domain (§5) before wandering into callers.
4. Inspect only **Related Source** columns and §4 “Then inspect” lists.
5. Skip §11 areas unless the task explicitly touches them.
6. If docs are insufficient, inspect code; if you confirm new rules or doc errors, **propose** updates to `docs/` (do not silently assume).
7. Respect `.cursor/rules/user-run-commands.mdc`: do not run tests/migrations/SQL unless the user asks—suggest commands instead.

---

# 2. Project Structure

| Area | Path | Purpose |
|------|------|---------|
| Backend app | `app/` | HTTP, models, services, Filament UI, support helpers |
| Public frontend | `resources/js/` | Inertia/Vue pages, layout, components |
| Public + PDF views | `resources/views/` | Blade root (`app.blade.php`), CV PDF (`cv/`) |
| Routes | `routes/web.php`, `routes/console.php` | Web only; no `routes/api.php` |
| Database | `database/migrations/`, `database/seeders/` | Schema and seeds |
| Lang | `lang/{vi,en}/` | UI + panel strings |
| Knowledge base | `docs/` | Business, architecture, workflows |
| Cursor rules | `.cursor/rules/*.mdc` | Short routing constraints for agents |
| Tests | `tests/Feature/` | Behavior specs (read when validating; user runs them) |
| Legacy (ignore) | `_legacy-react/` | Old React UI; not wired to Laravel routes |

There is **no** `app/Repositories/` layer. **No** custom `app/Jobs/` or domain events/listeners beyond framework/Filament defaults.

---

# 3. Business Domain Map

| Domain | Documentation | Main source (truth) | Related source |
|--------|---------------|---------------------|----------------|
| Portfolio core (slug, publish, 1:1 user) | `docs/business/portfolio.md` | `app/Observers/UserObserver.php`, `app/Http/Controllers/PortfolioController.php` | `app/Models/Portfolio.php`, `app/Filament/Resources/Users/UserResource.php` |
| Public site (Inertia) | `docs/business/public-site.md` | `app/Services/PortfolioPresenter.php` | `app/Http/Controllers/PortfolioController.php`, `resources/js/Pages/Portfolio/Show.vue`, `resources/js/Layouts/PublicLayout.vue` |
| CV (web + PDF) | `docs/business/cv.md` | `app/Services/PortfolioPresenter.php`, `resources/views/cv/pdf.blade.php` | `app/Filament/Pages/ManageCv.php`, `resources/js/Pages/Portfolio/Cv.vue` |
| i18n (content + URL locale) | `docs/business/i18n.md` | `app/Support/HasLocaleText.php`, `app/Support/LocaleCatalog.php` | `app/Filament/Forms/LocaleTabs.php`, `app/Http/Middleware/SetLocale.php`, `app/Models/Locale.php` |
| Appearance & themes | `docs/business/appearance.md` | `app/Support/AppearanceTheme.php` | `app/Filament/Pages/ManageAppearance.php`, `app/Models/Theme.php`, `app/Filament/Resources/Themes/ThemeResource.php` |
| Studio (owner Filament) | `docs/business/studio.md` | `app/Filament/Pages/SetupWizard.php`, `app/Services/PortfolioWizardSync.php` | `app/Providers/Filament/StudioPanelProvider.php`, `app/Filament/Pages/ManageProfile.php` |
| Content sections | `docs/business/content-sections.md` | `app/Services/PortfolioWizardSync.php` | `app/Filament/Resources/*` (Projects, Skills, …), section models under `app/Models/` |
| Auth & accounts | `docs/business/auth.md` | `app/Http/Controllers/Auth/*`, `app/Http/Requests/Auth/*` | `app/Models/User.php`, `resources/js/Pages/Auth/*` |
| Admin & impersonation | `docs/business/admin.md` | `app/Filament/Resources/Users/UserResource.php`, `app/Http/Controllers/ImpersonationController.php` | `app/Providers/Filament/AdminPanelProvider.php`, `app/Http/Middleware/AuthenticateAdmin.php` |
| Mail (SMTP/log) | `docs/business/mail.md` | `app/Models/MailSetting.php`, `app/Filament/Pages/ManageMail.php` | `app/Providers/AppServiceProvider.php` (mail.manager hook) |
| Translation (editor assist) | `docs/business/translation.md` | `app/Services/TextTranslator.php` | `app/Filament/Forms/RichEditor/FullEditorPlugin.php`, `app/Models/TranslationApi.php` |

**Architecture (cross-cutting):** `docs/architecture/overview.md`, `modules.md`, `data-flow.md`  
**Database (cross-cutting):** `docs/database/overview.md`, `important-relations.md`

---

# 4. Task → Where To Look

## Portfolio & publishing

| Task | Read first | Then inspect |
|------|------------|--------------|
| Slug / username / URL | `portfolio.md` | `UserObserver.php`, `PortfolioController.php`, `RegisterRequest.php` |
| Publish / unpublish / preview 404 | `portfolio.md`, `public-site.md` | `PortfolioController::canPreview`, `PortfolioPresenter` (unchanged for gate) |
| SEO title/description | `portfolio.md`, `content-sections.md` | `PortfolioPresenter` (`seo` key), `ManageProfile.php`, `SetupWizard.php` |
| New user → portfolio bootstrap | `portfolio.md`, `workflows/registration-onboarding.md` | `UserObserver.php` |

## Public site & landing

| Task | Read first | Then inspect |
|------|------------|--------------|
| Portfolio page layout / sections | `public-site.md`, `content-sections.md` | `Show.vue`, `PortfolioPresenter.php` |
| Theme CSS / layout / particles | `appearance.md`, `public-site.md` | `PublicLayout.vue`, `AppearanceTheme.php`, `PortfolioPresenter` (`appearance`) |
| Light/dark/system mode | `appearance.md` (§ two theme concepts) | `portfolios.default_theme`, `PublicLayout.vue` (`theme` prop) |
| Landing / demo link | `public-site.md` | `PortfolioController::landing`, `Landing.vue` |

## CV & PDF

| Task | Read first | Then inspect |
|------|------------|--------------|
| CV section visibility | `cv.md` | `CvSetting` model, `ManageCv.php`, `PortfolioPresenter` (`cv.settings`) |
| CV template (modern/classic/sidebar) | `cv.md`, `appearance.md` | `AppearanceTheme::syncCvLayout`, `ManageCv.php`, `Cv.vue`, `cv/pdf.blade.php` |
| PDF layout / DomPDF quirks | `cv.md` | `PortfolioController::cvPdf`, `resources/views/cv/**` |
| Web vs PDF parity | `cv.md` (Verification table) | `Cv.vue` vs `cv/partials/*.blade.php` |

## i18n

| Task | Read first | Then inspect |
|------|------------|--------------|
| URL locale 404 / enabled languages | `i18n.md` | `SetLocale.php`, `LocaleCatalog.php`, `LocaleResource.php`, `LocaleSeeder.php` |
| Multi-language field storage | `i18n.md` | `HasLocaleText.php`, `LocaleTabs.php`, model `$casts` JSON fields |
| Panel UI language | `i18n.md` | `UiLocale.php`, `ApplyUiLocale.php`, `lang/*/panel.php` |
| Public chrome strings | `i18n.md` | `lang/*/ui.php`, `HandleInertiaRequests.php` (`ui` prop) |

## Appearance

| Task | Read first | Then inspect |
|------|------------|--------------|
| Theme preset / colors / hero | `appearance.md` | `AppearanceTheme.php`, `ManageAppearance.php`, `ThemeResource.php` |
| Import/export theme JSON | `appearance.md` | `AppearanceTheme::import/export`, `ManageAppearance.php` header actions |
| Per-portfolio overrides | `appearance.md`, `database/important-relations.md` | `portfolio_themes` migration, `PortfolioTheme` model |

## Studio & content editing

| Task | Read first | Then inspect |
|------|------------|--------------|
| Setup wizard flow | `studio.md`, `workflows/registration-onboarding.md` | `SetupWizard.php`, `PortfolioWizardSync.php` |
| Wizard vs resource duplicate edits | `workflows/content-edit-sync.md`, `content-sections.md` | `PortfolioWizardSync.php` vs target `*Resource.php` |
| Profile page (non-wizard) | `content-sections.md` | `ManageProfile.php` |
| Repeater sync / delete missing rows | `workflows/content-edit-sync.md` | `PortfolioWizardSync::sync`, `saveSkills` |
| Avatar upload / preview | `studio.md`, `data-flow.md` | `WizardPendingAvatar.php`, `WizardAvatarController.php`, `ManageProfile.php` |
| Single section CRUD | `content-sections.md` | Matching `app/Filament/Resources/{Section}/` |

## Auth

| Task | Read first | Then inspect |
|------|------------|--------------|
| Register / reserved usernames | `auth.md` | `RegisterRequest.php`, `RegisterController.php` |
| Login redirect admin vs user | `auth.md` | `LoginController.php`, `AppServiceProvider` (`RedirectIfAuthenticated`) |
| Password reset email | `auth.md`, `mail.md` | `ForgotPasswordController.php`, `ResetPasswordNotification.php`, Auth Vue pages |

## Admin

| Task | Read first | Then inspect |
|------|------------|--------------|
| User admin / demote rules | `admin.md` | `UserResource.php` (`cannotDemoteAdmin`, `saveUser`) |
| Impersonation | `admin.md`, `workflows/admin-impersonation.md` | `ImpersonationController.php`, `User.php` `canAccessPanel` |
| Locales / themes / translation APIs | `admin.md` + domain doc | `LocaleResource`, `ThemeResource`, `TranslationApiResource` |

## Mail

| Task | Read first | Then inspect |
|------|------------|--------------|
| SMTP settings / test email | `mail.md` | `ManageMail.php`, `MailSetting.php` |
| Runtime mail config | `mail.md` | `AppServiceProvider.php`, `MailSetting::applyToConfig` |

## Translation tool

| Task | Read first | Then inspect |
|------|------------|--------------|
| Editor translate button | `translation.md` | `FullEditorPlugin.php`, `TextTranslator.php` |
| Provider order / drivers | `translation.md` | `TranslationApi.php`, `TranslationApiResource.php` |

## Filament / panel infra

| Task | Read first | Then inspect |
|------|------------|--------------|
| Studio vs admin panel diff | `architecture/overview.md`, `admin.md`, `studio.md` | `StudioPanelProvider.php`, `AdminPanelProvider.php` |
| Scoping data to current user | `portfolio.md`, `content-sections.md` | `Portfolio` global scope, `BelongsToPortfolio` trait |
| Clear cache button | `architecture/data-flow.md` | `ClearCacheController.php` |
| Rich editor plugins | [NEEDS_VERIFICATION] no dedicated doc | `FullRichEditor.php`, `resources/js/filament/rich-content-plugins/` |

---

# 5. Domain → Source of Truth

| Domain | Business rules doc | Implementation | Database |
|--------|-------------------|----------------|----------|
| Public payload shape | `public-site.md` | `PortfolioPresenter.php` | Section tables via presenter relations |
| Wizard bulk write | `content-sections.md`, `studio.md` | `PortfolioWizardSync.php` | `2026_09_11_000002_create_portfolio_tables.php` |
| Publish gate | `portfolio.md` | `PortfolioController.php` | `portfolios.is_published` |
| Slug ↔ username | `portfolio.md` | `UserObserver.php`, `UserResource::saveUser` | `users.username`, `portfolios.slug` |
| Visual theme on site | `appearance.md` | `AppearanceTheme.php` | `themes`, `portfolio_themes`, `portfolios.theme_id` |
| CV toggles & template | `cv.md` | `ManageCv.php`, `CvSetting` model | `cv_settings` |
| Enabled URL locales | `i18n.md` | `LocaleCatalog.php`, `SetLocale.php` | `locales` |
| Mail transport | `mail.md` | `MailSetting.php`, `ManageMail.php` | `mail_settings` |
| Machine translation | `translation.md` | `TextTranslator.php` | `translation_apis` |

Do not treat Filament Resources as source of truth for **public** output if `PortfolioPresenter` already maps the same data.

---

# 6. Cross-Domain Dependencies

```
User registration (auth)
  → UserObserver → Portfolio + Profile + CvSetting (portfolio)

Any public/CV page
  → PortfolioController → PortfolioPresenter
      → all section models (content-sections)
      → AppearanceTheme (appearance)
      → LocaleCatalog (available_locales)
      → CvSetting (cv.settings)

SetupWizard / ManageProfile / *Resource saves
  → section tables (content-sections)
  → optional AppearanceTheme::syncCvLayout (cv + appearance)

ManageAppearance / Theme apply
  → themes + portfolio_themes
  → cv_settings.template (cv)

Password reset / test mail (auth / mail)
  → MailSetting applied config (mail)

LocaleTabs / HasLocaleText (i18n)
  → all JSON locale fields (content + profile)

Admin impersonation (admin)
  → auth user swap → Studio scopes act as target portfolio (studio + portfolio)
```

---

# 7. Important Entry Points

| Kind | Location |
|------|----------|
| Web routes | `routes/web.php` |
| Console | `routes/console.php` (default `inspire` only) |
| HTTP controllers | `app/Http/Controllers/` |
| Filament Studio | `/studio` → `StudioPanelProvider`, home `/studio/setup` |
| Filament Admin | `/admin` → `AdminPanelProvider` |
| Middleware | `bootstrap/app.php`, `app/Http/Middleware/` |
| Inertia shared props | `HandleInertiaRequests.php` |
| Mail config bootstrap | `AppServiceProvider::boot` (mail.manager) |
| Livewire upload override | `LivewireFileUploadController.php` [NEEDS_VERIFICATION: when to read—upload bugs only] |

**Not present:** REST API routes, webhooks, queue workers, scheduled jobs, custom Artisan commands (domain-specific).

---

# 8. Database Map

| Model | Table | Related models | Used by |
|-------|-------|----------------|---------|
| User | users | Portfolio (1:1) | auth, admin, Filament |
| Portfolio | portfolios | User, Profile, sections, Theme | all domains |
| Profile | profiles | Portfolio | content, presenter |
| CvSetting | cv_settings | Portfolio | cv, wizard, appearance sync |
| Project, Education, … | matching tables | Portfolio | content-sections, presenter |
| SkillCategory / Skill | skill_categories, skills | Portfolio / category | content-sections |
| Theme | themes | PortfolioTheme | appearance, admin |
| PortfolioTheme | portfolio_themes | Portfolio, Theme | appearance |
| Locale | locales | — | i18n, admin |
| TranslationApi | translation_apis | — | translation, admin |
| MailSetting | mail_settings | — | mail |

Full ER notes: `docs/database/important-relations.md`.

---

# 9. Frontend Map

| Feature | Pages / components | Backend / data |
|---------|-------------------|----------------|
| Landing | `Pages/Landing.vue` | `PortfolioController::landing` |
| Public portfolio | `Pages/Portfolio/Show.vue`, `PublicLayout.vue` | `PortfolioPresenter`, `Show.vue` props |
| Public CV | `Pages/Portfolio/Cv.vue` | same presenter + `cv.*` props |
| Auth | `Pages/Auth/*.vue` | Auth controllers |
| Locale switcher | `Components/LocaleSelect.vue` | `available_locales` / shared `locales` |
| Rich HTML sections | `Components/HtmlContent.vue` | `localeHtml` output in presenter |
| Filament (owner/admin) | Livewire/Filament PHP + Blade views under `resources/views/filament/` | `app/Filament/**` |
| Filament JS plugin | `resources/js/filament/rich-content-plugins/font-size.js` | registered in `AppServiceProvider` |

No separate SPA API client—Inertia props only on public pages.

---

# 10. Common Search Patterns

| Domain | Keywords / patterns |
|--------|---------------------|
| Portfolio | `is_published`, `canPreview`, `slug`, `UserObserver` |
| Presenter | `PortfolioPresenter`, `publicPayload` |
| Wizard | `PortfolioWizardSync`, `SetupWizard`, `persistStep`, `saveAll` |
| i18n | `LocaleCatalog`, `HasLocaleText`, `localeText`, `LocaleTabs`, `_locale` |
| Appearance | `AppearanceTheme`, `theme_id`, `portfolio_themes`, `default_theme` |
| CV | `CvSetting`, `cv_layout`, `show_about`, `cv.pdf` |
| Auth | `RegisterRequest`, `is_admin`, `canAccessPanel` |
| Impersonation | `impersonator_id`, `ImpersonationController` |
| Mail | `MailSetting`, `writeConfig`, `applyToConfig` |
| Translation | `TextTranslator`, `TranslationApi`, `translateSelection` |
| Scoping | `current_user`, `current_portfolio`, `BelongsToPortfolio`, `Filament::isServing` |

---

# 11. What NOT To Read Initially

| Skip unless task-related | Reason |
|--------------------------|--------|
| `vendor/`, `node_modules/` | Dependencies |
| `public/build/`, `public/hot`, Filament published assets | Generated/build |
| `_legacy-react/` | Not used by Laravel app |
| `storage/`, `bootstrap/cache/` | Runtime |
| `tests/` | User runs tests; read specific test file only when verifying behavior |
| Unrelated Filament Resources | e.g. skip `ThemeResource` for a pure CV toggle task |
| Full `AppearanceTheme.php` | Read when changing themes; skip for auth-only tasks |
| All `database/migrations/` | Use `docs/database/*` + one migration when changing schema |
| `composer.lock`, `package-lock` | Lockfiles |
| `docker/` | Deployment only |

---

# 12. Documentation Priority

1. **`docs/AGENT_MAP.md`** (this file)
2. **`docs/AGENT_GUIDE.md`** (process + domain keywords)
3. **`docs/business/<domain>.md`** for the task
4. **`docs/workflows/<flow>.md`** if the task crosses steps or UI paths
5. **`.cursor/rules/*.mdc`** matching globs (`business-rules`, `architecture`, `user-run-commands`)
6. **Source of truth** file(s) from §5
7. **Related source** from §3 / §4
8. **`docs/database/*`** only for schema/relations questions
9. **`docs/architecture/*`** for panel/routing/middleware questions
10. Broader grep only if still blocked

---

# 13. Documentation Gaps

No separate doc yet (use code + tests; propose new `docs/business/*.md` if recurring):

- `[DOCUMENTATION_MISSING]` Dedicated **Filament panel / resource visibility** matrix (which resource is admin-only vs studio)
- `[DOCUMENTATION_MISSING]` **Livewire file upload** / avatar pipeline (`LivewireFileUploadController`, temp uploads)
- `[DOCUMENTATION_MISSING]` **Password reset** end-to-end workflow (partially covered in `auth.md` + `mail.md`)
- `[DOCUMENTATION_MISSING]` **Landing** marketing page behavior beyond demo slug
- `[DOCUMENTATION_MISSING]` **Rich editor** custom plugins (font-size, translate) beyond `translation.md`
- `[DOCUMENTATION_MISSING]` **Getting started** / `PortfolioGuide` widget copy and steps

Domains **not applicable** to this project (no docs expected): Booking, Order, Payment, Pricing, Affiliate, Inventory, Product catalog.

---

## Mappings marked `[NEEDS_VERIFICATION]`

- **Rich editor / Filament JS plugins:** exact extension points when task is not translate/font-size related.
- **Livewire upload controller:** read only for upload/temp-file bugs unless a doc is added later.
