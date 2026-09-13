# Public site (Inertia)

## Purpose

Render the marketing landing page and each owner's portfolio/CV for anonymous and authenticated visitors, with locale in the URL and theme appearance applied client-side.

## Business Rules

- Portfolio pages live under **`/{locale}/{username}`** where locale must be **enabled** in `locales` table or **404** (`SetLocale`).
- Username in URL is **`portfolios.slug`**, not numeric id.
- **`HandleInertiaRequests`** runs for public routes only (not `/admin`, `/studio`, `/livewire`).
- Shared Inertia props: `locale`, `locales`, `ui` translations, `auth` summary, `theme` (= color mode default `system`).
- Page-specific props from `PortfolioPresenter` include `appearance`, `profile`, sections, `cv` URLs, `available_locales`.
- Owner/admin may view **unpublished** portfolios; others get 404.

## Workflow

1. Request hits localized route group → `SetLocale` sets `UiLocale` + app locale from URL segment.
2. `PortfolioController::show|cv` loads portfolio by slug → publish check → presenter payload.
3. Vue layout reads `appearance` to set CSS variables and `data-layout`, `data-hero`, etc.
4. `PublicLayout` applies `theme` prop for light/dark/system preference.

## Data

Presenter output only — no direct model exposure to Vue. See `docs/business/portfolio.md` and section-specific docs.

## Source of Truth

- Routing + gates: `app/Http/Controllers/PortfolioController.php`
- Payload shape: `app/Services/PortfolioPresenter.php`
- Locale middleware: `app/Http/Middleware/SetLocale.php`
- Shared props: `app/Http/Middleware/HandleInertiaRequests.php`
- UI application: `resources/js/Layouts/PublicLayout.vue`, `resources/js/Pages/Portfolio/Show.vue`

## Important Edge Cases

- SEO title falls back to `{full_name} | Portfolio`; description falls back to localized tagline.
- Avatar in payload may be pending wizard URL (auth-only route) for preview, or `/storage/...` when committed.
- Landing page does not use presenter; only passes optional `demoUrl`.

## Related Files

- `resources/js/Pages/Landing.vue`
- `resources/js/Components/LocaleSelect.vue`

## Common Mistakes

- Adding public fields only in Vue without extending `PortfolioPresenter` (PDF/CV won't get them).
- Using disabled locale codes in links — must match `LocaleCatalog::enabled()`.
- Enabling Inertia on Filament routes (currently explicitly skipped).

## Verification

- `is_featured` is stored and edited in Studio but **not** used in `PortfolioPresenter` or Vue — all projects are shown ordered by `sort_order`.
