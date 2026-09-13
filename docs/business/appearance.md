# Appearance & themes

## Purpose

Control visual design of the public portfolio (layout, hero, colors, typography density) via a **catalog of themes** plus per-portfolio overrides, separate from light/dark **color mode**.

## Business Rules

### Color mode (not appearance preset)

- Field: `portfolios.default_theme` — values `system`, `light`, `dark`.
- Exposed to Vue as Inertia prop **`theme`** (see `PublicLayout.vue`).

### Visual theme (appearance)

- Catalog table **`themes`**: slug matches preset ids (`aurora`, `midnight`, …); seeded from `AppearanceTheme::presets()`.
- Portfolio assigns **`theme_id`**; overrides stored in **`portfolio_themes.customization`** as **diff only** vs catalog definition.
- Resolution: `AppearanceTheme::resolve()` = catalog definition + customization diff normalized.
- Public output: `AppearanceTheme::publicFor()` adds `css` and `dark_css` variable maps.
- Import/export JSON schema: `portfotilo.theme.v1` (`AppearanceTheme::SCHEMA`).
- **At least one enabled theme** must exist; default theme cannot be disabled if last enabled (`Theme::canBeDisabled`).
- Only **one** `is_default` theme; saving one clears others.

### CV layout linkage

- Each theme has default `cv_layout`; portfolio `cv_settings.template` updated when applying theme or syncing CV layout.

### Legacy

- Column `portfolios.appearance` populated pre-migration; migrated to `theme_id` + `portfolio_themes` in migration `2026_09_12_000009`. Not read in `resolve()` at runtime.

## Workflow

1. Admin maintains catalog (`ThemeResource`) — enable/disable, sort, defaults.
2. Owner opens **Manage Appearance** → selects preset → optional color/layout tweaks → save.
3. `AppearanceTheme::apply()` writes `theme_id`, upserts customization diff, syncs CV template.
4. Public page loads resolved appearance in presenter.

## Data

| Table | Role |
|-------|------|
| `themes` | Global presets + admin-editable catalog |
| `portfolio_themes` | Per-portfolio overrides per theme_id |
| `portfolios.theme_id` | Active catalog theme |
| `cv_settings.template` | Kept in sync with resolved `cv_layout` |

## Source of Truth

- Logic: `app/Support/AppearanceTheme.php`
- Admin catalog: `app/Filament/Resources/Themes/ThemeResource.php`, `app/Models/Theme.php`
- Owner UI: `app/Filament/Pages/ManageAppearance.php`
- Wizard CV template step also calls `AppearanceTheme::syncCvLayout` via `PortfolioWizardSync`

## Important Edge Cases

- `assignedTheme()` falls back to `Theme::defaultEnabled()` if portfolio theme disabled/missing.
- Preset hardcoded arrays in `AppearanceTheme::presets()` still used for seeding and normalization fallback (`aurora`).
- Reset appearance deletes customization row for current catalog theme only.

## Related Files

- `app/Filament/Forms/ThemeAppearanceFields.php`
- `database/migrations/2026_09_12_000009_create_themes_and_portfolio_themes_tables.php`

## Common Mistakes

- Confusing `default_theme` (light/dark) with preset slug.
- Writing full theme JSON into `portfolio_themes.customization` instead of diff (apply stores diff only).
- Editing `cv_settings.template` without `syncCvLayout` when changing from appearance side [ManageCv and wizard already sync — follow those patterns].

## Verification

- Admins can create **custom themes** in `ThemeResource` (any unique `slug` + layout/colors). Owners pick enabled themes by **`theme_id`** in Manage Appearance (`Theme::enabledOptions()`).
- Public site does **not** branch on hardcoded preset keys; Vue uses resolved fields (`layout`, `hero`, `colors`, `css`, …) from `AppearanceTheme::publicFor()`. The `preset` value in payload is the theme **slug** (catalog or custom).

