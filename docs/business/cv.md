# CV (web + PDF)

## Purpose

Generate a printable curriculum vitae from the same portfolio data as the public site, with per-owner section visibility and layout template.

## Business Rules

- Routes: `/{locale}/{username}/cv` (Inertia), `.../cv.pdf` (download).
- Same **publish/preview** rules as portfolio show.
- **`cv_settings`** row (1:1 portfolio) controls:
  - `template`: `modern` \| `classic` \| `sidebar`
  - Section toggles: about, skills, projects, education, languages, principles, avatar
- **`show_principles` defaults false** at DB level; wizard defaults match.
- Presenter sets **`show_avatar` false** when no avatar file exists even if toggle true.
- PDF filename: `{slug-full-name}-cv-{locale}.pdf`.
- PDF avatars: HTTP URLs converted to **base64** from `public` disk if under `/storage/` (Dompdf constraint).

## Workflow

1. `PortfolioController` builds presenter payload (includes `cv.settings`, `cv.url`, `cv.pdf_url`).
2. **Web CV**: `Portfolio/Cv.vue` picks layout from `appearance.cv_layout` **or** `settings.template`.
3. **PDF**: `CvPdfExporter` renders `resources/views/cv/pdf.blade.php` via Dompdf (separate HTML/CSS from the Inertia page; tuned for print-like layout).

## Data

| Field / table | Role |
|---------------|------|
| `cv_settings` | Toggles + template string |
| `appearance.cv_layout` | Resolved theme field (should align with template after sync) |

## Source of Truth

- Settings CRUD: `app/Filament/Pages/ManageCv.php`
- Wizard CV step: `SetupWizard` → `PortfolioWizardSync` (updates `cv_settings` + `AppearanceTheme::syncCvLayout`)
- Public embedding: `PortfolioPresenter` (`cv` key)
- PDF generation: `App\Services\CvPdfExporter`, `PortfolioController::cvPdf`, `resources/views/cv/pdf.blade.php`

## Important Edge Cases

- Changing template in **Manage Appearance** runs `AppearanceTheme::apply` → updates `cv_settings.template`.
- Changing template in **Manage CV** calls `AppearanceTheme::syncCvLayout` → may update theme customization linkage.
- Three templates share partials under `resources/views/cv/partials/`.

## Related Files

- `resources/js/Pages/Portfolio/Cv.vue`
- `app/Support/AppearanceTheme.php` (`cvLayoutOptions`, `syncCvLayout`)

## Common Mistakes

- Editing only Blade without updating presenter toggles (sections still gated in PHP array).
- Expecting external image URLs in PDF without base64 conversion (only local storage paths handled).
- Duplicating CV field mapping in PDF instead of reusing presenter [presenter is shared — good pattern to keep].

## Verification

**Section toggles** (`show_about`, `show_skills`, `show_projects`, `show_education`, `show_languages`, `show_principles`, `show_avatar`) — same gates in `Cv.vue` and `cv/partials/*.blade.php` / PDF header.

**Presentation differences** (same data, not pixel-identical):

| Aspect | Web `Cv.vue` | PDF `pdf.blade.php` + partials |
|--------|----------------|----------------------------------|
| Project body | `summary \|\| solution` | `solution ?: summary` |
| Project extras | subtitle, `tech_stack` chips | no subtitle / tech chips |
| Layout class | `cv-modern`: accent bar, timeline, skill chips · `cv-classic`: serif, centered rules, inline contact · `cv-sidebar`: colored rail |
| Principles HTML | `HtmlContent` component | raw `{!! !!}` |

Both honor empty sections (toggle on but no content → section hidden).
