# Project Knowledge Base

Portfotilo — Laravel + Inertia/Vue portfolio SaaS. Human index below; **agents** start with [AGENT_MAP.md](AGENT_MAP.md) then [AGENT_GUIDE.md](AGENT_GUIDE.md).

## Architecture

- [Overview](architecture/overview.md) — stacks, entry points, tenancy, dual locale/theme concepts
- [Modules](architecture/modules.md) — folders, services, Filament vs public
- [Data flow](architecture/data-flow.md) — read/write paths, appearance, cache

## Business Domains

| Domain | Doc |
|--------|-----|
| Portfolio core (slug, publish, 1:1 user) | [business/portfolio.md](business/portfolio.md) |
| Public Inertia site | [business/public-site.md](business/public-site.md) |
| CV web + PDF | [business/cv.md](business/cv.md) |
| i18n (content JSON + UI locale) | [business/i18n.md](business/i18n.md) |
| Appearance & theme catalog | [business/appearance.md](business/appearance.md) |
| Studio (owner Filament) | [business/studio.md](business/studio.md) |
| Admin & impersonation | [business/admin.md](business/admin.md) |
| Auth & registration | [business/auth.md](business/auth.md) |
| Translation assist | [business/translation.md](business/translation.md) |
| Mail (SMTP / log) | [business/mail.md](business/mail.md) |
| Content sections (skills, projects, …) | [business/content-sections.md](business/content-sections.md) |

## Workflows

- [Registration & onboarding](workflows/registration-onboarding.md)
- [Public view & CV download](workflows/public-view-and-cv.md)
- [Content edit: wizard vs resources](workflows/content-edit-sync.md)
- [Admin impersonation](workflows/admin-impersonation.md)

## Database

- [Overview](database/overview.md)
- [Important relations](database/important-relations.md)

## Rules (Cursor)

Tracked in git under `.cursor/rules/` (folder may look hidden in Explorer; still clones on `git pull`). Other `.cursor/` paths stay local via `.gitignore`.

- [.cursor/rules/project-context.mdc](../.cursor/rules/project-context.mdc) — where docs live
- [.cursor/rules/business-rules.mdc](../.cursor/rules/business-rules.mdc) — domain pointers
- [.cursor/rules/architecture.mdc](../.cursor/rules/architecture.mdc) — structure pointers
- [.cursor/rules/documentation-guide.mdc](../.cursor/rules/documentation-guide.mdc) — maintaining docs
- [.cursor/rules/user-run-commands.mdc](../.cursor/rules/user-run-commands.mdc) — tests/migrations/DB: user runs, agent suggests

## Agent onboarding

**Người giao việc:** [PROMPT_GUIDE.md](PROMPT_GUIDE.md) — cách viết prompt (feature, bug, …).

**Agent:**

1. [AGENT_MAP.md](AGENT_MAP.md) — task/domain routing, what to read and skip  
2. [AGENT_GUIDE.md](AGENT_GUIDE.md) — process, safety, when to open code  
3. This README — domain index and **Source of Truth** table

## Source of Truth

| Concern | Primary location |
|---------|------------------|
| Public JSON/props for site & PDF | `app/Services/PortfolioPresenter.php` |
| Wizard bulk save / repeater sync | `app/Services/PortfolioWizardSync.php` |
| Visual theme resolve/apply | `app/Support/AppearanceTheme.php` |
| Locale catalog & enabled codes | `app/Support/LocaleCatalog.php` + `app/Models/Locale.php` |
| Localized field read | `app/Support/HasLocaleText.php` |
| Localized field write (forms) | `app/Filament/Forms/LocaleTabs.php` |
| User → portfolio bootstrap | `app/Observers/UserObserver.php` |
| Publish / preview gate | `app/Http/Controllers/PortfolioController.php` |
| Machine translation | `app/Services/TextTranslator.php` |
| HTTP routes | `routes/web.php` |
| Admin impersonation | `app/Http/Controllers/ImpersonationController.php` |
| Account disable / soft delete / audit | `app/Services/UserAccountService.php`, `app/Services/AccountAuditLogger.php` |
| Account audit log (admin UI) | `app/Filament/Resources/AccountAuditLogs/AccountAuditLogResource.php` |
| Theme catalog DB | `app/Models/Theme.php` + `ThemeResource` |
| CV section toggles | `app/Models/CvSetting.php` + `ManageCv` / wizard |
| Mail transport & from address | `app/Models/MailSetting.php` + `ManageMail` |

## Out of scope / legacy

- `_legacy-react/` — reference UI only, not wired to Laravel routes
- Root `README.md` — setup commands for humans
