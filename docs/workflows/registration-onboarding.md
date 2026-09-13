# Workflow: Registration & onboarding

## Trigger

Guest submits `/register` with valid `RegisterRequest` data.

## Steps

1. **User row created** (`RegisterController`).
2. **`UserObserver::created`**
   - Portfolio: `slug = username`, `is_published = false`, `default_locale = LocaleCatalog::defaultCode()`, `default_theme = system`.
   - Profile: `full_name` from user name, email copied, empty JSON bags for text fields.
   - `CvSetting` row with DB defaults.
3. User is logged in; redirect to `/studio/setup`.
4. Redirect to **Studio** → **`SetupWizard`** (`/studio/setup`) as home URL.
5. Wizard steps (skippable, query-string step persistence):
   - Profile (username, publish, locale, SEO, avatar, localized bio)
   - Skills → Projects → Background → Presence → CV → Preview
6. Each validated step calls **`persistStep`** → `PortfolioWizardSync::saveAll` (avatar not committed until final save).
7. Finish submit → **`save`** with `commitAvatar: true` → `WizardPendingAvatar::commit`.

## Outcomes

- Owner can preview unpublished site (owner/admin gate).
- Publishing sets `is_published` true (still requires enabled default locale URLs).

## Key files

- `app/Observers/UserObserver.php`
- `app/Filament/Pages/SetupWizard.php`
- `app/Services/PortfolioWizardSync.php`
- `app/Support/WizardPendingAvatar.php`

## Failure modes

- Username collision handled at validation; observer slug sync on username change.
- Avatar commit failure leaves pending session until cleared.
