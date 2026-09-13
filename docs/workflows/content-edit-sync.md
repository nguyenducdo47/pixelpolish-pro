# Workflow: Editing content (wizard vs resources)

## Context

Two UIs write the same tables. No event bus — last write wins.

## Path A — Setup wizard

1. User edits step form state (`SetupWizard::$data`).
2. On step validation: `persistStep` → `persistableData()` strips avatar unless committing.
3. `PortfolioWizardSync::saveAll`:
   - Profile + user username + portfolio flags
   - Repeaters: upsert by `id`, reindex `sort_order`, delete removed ids
   - CV settings + `AppearanceTheme::syncCvLayout`
4. Preview step may call `previewUrls()` which **persists current form** before building URLs.

## Path B — Filament resource / page

1. Standard Filament save on single model or `ManageProfile` / `ManageCv` / `ManageAppearance`.
2. **`ManageCv::save`** updates `cv_settings` then `syncCvLayout`.
3. **`ManageAppearance::persist`** calls `AppearanceTheme::apply` (updates theme + CV template).

## Public read (always)

Regardless of edit path, **`PortfolioPresenter`** is the only public serializer.

## When adding a field

1. Migration + model `$fillable` / casts
2. `LocaleTabs` in wizard **and** resource if applicable
3. `PortfolioWizardSync` (`formState`, `saveAll` / field lists)
4. `PortfolioPresenter` mapping
5. Vue section + CV blade if displayed

## Key files

- `app/Services/PortfolioWizardSync.php`
- `app/Filament/Pages/SetupWizard.php`
- Section resources under `app/Filament/Resources/`
