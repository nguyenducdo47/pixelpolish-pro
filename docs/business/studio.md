# Studio (owner panel)

## Purpose

Filament panel at **`/studio`** where any authenticated user manages their own portfolio content, appearance, CV settings, and setup wizard.

## Business Rules

- Panel id: **`studio`**; default home **`/studio/setup`** (`StudioPanelProvider`, `Studio::home()`).
- Auth: `AuthenticateStudio` → redirects guests to `/login`.
- **All users** pass `User::canAccessPanel` for studio (including admins).
- Same Filament resources discovered as admin, but:
  - Admin-only resources hidden via `canViewAny()` (Users, Locales, Themes, TranslationApis).
  - `ManageMail` admin-only via `canAccess()`.
- Portfolio data scoped by global scopes (`Portfolio`, `BelongsToPortfolio`).
- Header hooks: locale switcher, clear cache, impersonation banner when `session('impersonator_id')` set.

## Workflow

Typical owner journey:

1. Register/login → redirect to Studio setup wizard.
2. Complete wizard steps (auto-save on step validation) or use sidebar resources.
3. Manage Appearance / CV as needed.
4. Publish from wizard or profile fields.

## Data

Uses same models as public site; no separate DTO layer except wizard form state from `PortfolioWizardSync::formState`.

## Source of Truth

- Panel config: `app/Providers/Filament/StudioPanelProvider.php`
- Onboarding: `app/Filament/Pages/SetupWizard.php`
- Bulk save: `app/Services/PortfolioWizardSync.php`
- Getting started hints: `app/Support/PortfolioGuide.php`, `GettingStartedWidget`

## Important Edge Cases

- Wizard avatar uses **pending session file** until final save with `commitAvatar: true`.
- `previewUrls()` in wizard calls `saveAll` before generating links (side effect persist).
- File uploads: `Profile` avatars `storeFiles(false)` on wizard — requires Livewire temp pipeline.

## Related Files

- Individual `app/Filament/Resources/*` (projects, skills, …)
- `app/Filament/Pages/ManageProfile.php`, `ManageCv.php`, `ManageAppearance.php`

## Common Mistakes

- Testing admin-only features while logged in as normal user on Studio (expected 403/hidden nav).
- Creating portfolio records without `portfolio_id` outside Filament (observers won't run for orphan rows).
- Assuming wizard is the only editor — resource pages can desync if user edits both without refresh [same DB — last write wins].

## Verification

- Pages such as `SetupWizard` and `ManageProfile` call `auth()->user()->portfolio` without null guards. A user without portfolio (e.g. created only via raw SQL) would error — **unsupported**; normal register/admin create paths always run `UserObserver::created`.
