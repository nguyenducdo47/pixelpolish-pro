# Agent Guide

## 1. Read first (every new task)

1. **`docs/AGENT_MAP.md`** — domain + task routing, source of truth pointers, what to skip  
2. **`docs/AGENT_GUIDE.md`** (this file) — process and constraints  
3. **`docs/README.md`** — domain index + **Source of Truth** table when you need a canonical file path  
4. **`docs/architecture/overview.md`** — if task touches routing, auth, or panels  

Then open **one business doc** for the feature area (see §2; full task tables in **AGENT_MAP §3–§4**).

## 2. Map task → domain

| Keywords / area | Business doc |
|-----------------|--------------|
| slug, publish, preview, username URL | `business/portfolio.md` |
| Show page, landing, Inertia props, SEO | `business/public-site.md` |
| CV, PDF, cv_settings, template | `business/cv.md` |
| locale JSON, LocaleTabs, URL `/vi/` | `business/i18n.md` |
| colors, theme preset, appearance | `business/appearance.md` |
| `/studio`, wizard, Filament owner | `business/studio.md` |
| `/admin`, users, impersonate | `business/admin.md` |
| login, register, password | `business/auth.md` |
| translate editor, TranslationApi | `business/translation.md` |
| SMTP, mail settings, test email | `business/mail.md` |
| projects, skills, repeaters | `business/content-sections.md` |

Workflow doc when changing **flow** across files:

- Onboarding → `workflows/registration-onboarding.md`  
- Public request path → `workflows/public-view-and-cv.md`  
- Wizard + resources → `workflows/content-edit-sync.md`  
- Impersonation → `workflows/admin-impersonation.md`  

Schema questions → `database/important-relations.md`.

## 3. Find docs quickly

- All paths listed in `docs/README.md`.  
- **Source of Truth** table names the canonical PHP file — open that file next, not random callers.  
- Grep the repo only after docs; search for class names from the table.

## 4. When to inspect source

Open code when:

- Doc marks **[NEEDS_VERIFICATION]** for your exact question  
- You add/remove a field (must sync presenter, wizard, migrations — see `workflows/content-edit-sync.md`)  
- Bug in Filament UI or Vue — docs describe behavior, not every component prop  

Avoid:

- Full-tree scans (`app/**`, `resources/**`) for business questions answered in `docs/business/*`  
- Reading `_legacy-react/` unless task says legacy/reference  
- Duplicating `PortfolioPresenter` mapping logic elsewhere  

## 5. Editing code safely

- **Two theme concepts:** `default_theme` (light/dark/system) vs `AppearanceTheme` (visual preset).  
- **Two edit paths:** wizard sync vs Filament resources — keep both updated.  
- **Filament scopes:** portfolio queries in panel are auto-scoped to current user.  
- **Public data:** extend `PortfolioPresenter` for anything visitors/PDF must see.  

## 6. Keep docs honest

If you discover a rule enforced in code but missing/wrong in docs:

1. Fix the relevant `docs/business/*.md` or workflow in the same PR/task when practical.  
2. Mark uncertain behavior **`[NEEDS_VERIFICATION]`** until confirmed in code/tests.  
3. Do **not** invent business rules — cite behavior from tests or PHP.  

## 7. Tests as spec hints

- `tests/Feature/AuthTest.php` — auth  
- `tests/Feature/TextTranslatorTest.php`, `TranslationApiTest.php` — translation  
- `tests/Feature/LivewireTempUploadTest.php` — avatar upload  

Run targeted tests after changes: `php artisan test --filter=...`
