# Portfolio content sections

## Purpose

Structured resume/portfolio blocks: profile, social links, skills, projects, education, spoken languages, principles — each owned by one portfolio and ordered for display.

## Business Rules

- All section models (except `Skill`) use **`BelongsToPortfolio`** → auto `portfolio_id` on create + Filament scope.
- **`Skill`** belongs to **`SkillCategory`** (category has `portfolio_id`); delete category cascades skills.
- Default ordering: **`sort_order` ascending** on relations defined on `Portfolio` model.
- **Projects** `is_featured` default `true` at DB; presenter exports all projects (see public-site verification).
- **Social links**: platform enum in wizard; URL required.
- **Principles** optional on CV (`show_principles` default false).

## Workflow

### Dual edit paths (source of truth for writes)

| Section | Wizard repeater | Filament resource |
|---------|-----------------|-------------------|
| Profile + publish | Setup wizard profile step | `ManageProfile`, wizard |
| Skills | wizard | `SkillCategoryResource` (+ relation manager) |
| Projects | wizard | `ProjectResource` |
| Education | wizard | `EducationResource` |
| Languages | wizard | `SpokenLanguageResource` |
| Social | wizard | `SocialLinkResource` |
| Principles | wizard | `PrincipleResource` |

**Write logic for wizard:** `PortfolioWizardSync` (sync by id, delete missing rows).

**Write logic for resources:** standard Filament Eloquent saves (single row).

**Read logic for public:** always `PortfolioPresenter` (not Filament).

## Data

See `database/migrations/2026_09_11_000002_create_portfolio_tables.php` for column types (JSON vs string).

## Source of Truth

- Public read mapping: `app/Services/PortfolioPresenter.php`
- Wizard bulk sync: `app/Services/PortfolioWizardSync.php`
- Filament forms duplicate field definitions with `LocaleTabs` — keep in sync when adding fields.

## Important Edge Cases

- Empty locale bag → empty string on public site, not fallback to other sections.
- Skill `level` 0–100 tinyint; wizard defaults 70.
- Deleting all repeater rows in wizard **deletes DB rows** (`sync` with empty keep list).

## Related Files

- `app/Models/Project.php`, `Skill.php`, `SkillCategory.php`, etc.
- `app/Filament/Resources/**`

## Common Mistakes

- Adding field to resource form but not wizard sync arrays (`saveAll` field lists) and presenter map.
- Querying skills without category scope (skill has no direct portfolio_id).

## Verification

- **`ManageProfile`** covers the same portfolio + profile fields as the wizard **profile step** (username, publish, locale, color mode, SEO, contact, avatar, LocaleTabs copy). It saves via inline Eloquent updates, **not** `PortfolioWizardSync`.
- **Avatar difference**: wizard uses `storeFiles(false)` + `WizardPendingAvatar` until commit; `ManageProfile` uploads directly to `avatars/` on save.
- Keep wizard `saveProfile` and `ManageProfile::save` in sync when adding profile-level fields.
