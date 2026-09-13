# Workflow: Public view & CV download

## Trigger

HTTP GET to localized portfolio or CV routes.

## Steps

1. **`SetLocale`**: validate `{locale}` enabled; set app + session UI locale.
2. **`PortfolioController`**: find portfolio by `slug = username`; **404** if missing.
3. **Publish gate**: if not `is_published`, allow only owner or admin.
4. **`PortfolioPresenter::publicPayload($portfolio, $locale)`**
   - Eager-load relations
   - Resolve avatar (pending wizard URL if owner previewing [auth on avatar route only])
   - Map all sections to arrays
   - Build `appearance`, SEO, CV URLs, locale switcher links
5. **Branch**
   - `show` → Inertia `Portfolio/Show`
   - `cv` → Inertia `Portfolio/Cv`
   - `cv.pdf` → optional avatar base64 → DomPDF `cv.pdf` view → download

## Frontend

- `PublicLayout` injects CSS variables from `appearance`.
- `Show.vue` toggles particles/glow from `appearance.hero` / `show_particles`.
- `Cv.vue` template from `appearance.cv_layout` ?? `cv.settings.template`.

## Key files

- `routes/web.php` (route order: auth routes before `{locale}` group)
- `app/Http/Controllers/PortfolioController.php`
- `app/Services/PortfolioPresenter.php`

## Failure modes

- Disabled locale → 404 at middleware (not controller).
- Missing storage file for avatar → null avatar; `show_avatar` may false in payload.
