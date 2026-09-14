# Portfolio (core aggregate)

## Purpose

Represents one owner's public site identity: URL slug, publish flag, SEO, default public locale, color mode, and link to visual theme. All portfolio content hangs off this row.

## Business Rules

- Exactly **one portfolio per user** (`portfolios.user_id` unique); created automatically on user registration.
- **`slug` must match `users.username`**; username changes propagate to slug (`UserObserver::updated`, wizard/admin user save).
- **`is_published` false** → public routes return **404** unless viewer is owner or admin (`PortfolioController::canPreview`).
- **`default_locale`** must be a valid enabled locale code [enforced in forms, not DB FK].
- Public routes identify portfolio by **`slug`**, route param named `username`.
- Reserved usernames blocked at registration (`RegisterRequest::notIn` list includes `admin`, `studio`, etc.).

## Workflow

1. User registers → observer creates portfolio + profile + cv_settings.
2. Owner edits content in Studio (wizard or resources).
3. Owner sets `is_published` (wizard profile/preview steps or admin user form).
4. Visitor hits `/{locale}/{slug}` → presenter loads all relations ordered by `sort_order`.

## Data

| Table / model | Notes |
|---------------|--------|
| `portfolios` | `slug`, `is_published`, `default_locale`, `default_theme`, `seo_*`, `theme_id` |
| `users` | `username` drives slug |
| Child tables | All `portfolio_id` FK, cascade delete |

Legacy column `portfolios.appearance` (JSON) was migration source for `theme_id`; **runtime resolution uses `AppearanceTheme` + DB themes**, not this column.

## Source of Truth

- Lifecycle / slug sync: `app/Observers/UserObserver.php`
- Publish + preview gate: `app/Http/Controllers/PortfolioController.php`
- Filament scope (owner sees only own row): `app/Models/Portfolio.php` global scope
- Admin bulk publish/locale: `UserResource::saveUser`

## Important Edge Cases

- Querying `Portfolio` inside Filament always filters to current user; admin impersonating sees **target** user's portfolio (same scope, different auth id).
- Landing demo URLs come from `config/content_profiles.php` (`demo_slugs`); IT demo remains `nguyenducdo`, other profiles use `demo-*` slugs when seeded.
- `Portfolio::publicUrl()` / `cvUrl()` use `default_locale` when locale omitted.

## Related Files

- `routes/web.php`
- `app/Services/PortfolioPresenter.php`
- `database/migrations/2026_09_11_000002_create_portfolio_tables.php`

## Common Mistakes

- Assuming admins can query all portfolios through Eloquent in Studio without `withoutGlobalScopes()`.
- Changing slug only on `portfolios` without updating `users.username`.
- Treating `default_theme` as the Aurora/Midnight preset (that is `theme_id` / `AppearanceTheme`).

## Verification

- Column `portfolios.appearance` remains in schema/fillable but **no application code writes it** after theme migration; reads use `AppearanceTheme::resolve()`. Only the migration `2026_09_12_000009` reads legacy JSON once.
