# Translation (authoring assist)

## Purpose

Optional machine translation inside the rich text editor to help authors fill non-default locale fields. Not automatic i18n sync.

## Business Rules

- Tool: `translateSelection` on `FullRichEditor` / `FullEditorPlugin`.
- Service tries **`TranslationApi::active()`** in `sort_order` until one returns text.
- If DB table empty/unavailable, falls back to **in-memory default models** (same URLs as seed defaults).
- Drivers: `google_chrome`, `google`, `mymemory` — unsupported driver skipped.
- Chunks text **>400 chars** by sentence boundaries.
- MyMemory rejects responses containing `MYMEMORY WARNING` or non-200 `responseStatus`.
- HTTP client retries once with **SSL verify disabled** on failure.
- Failure throws `RuntimeException` → user notification in UI.

## Workflow

1. Author selects text in editor or pastes in modal.
2. Chooses source (or auto) and target locale.
3. `TextTranslator::translate` → inserts result at cursor via editor command.

## Data

- `translation_apis` table (admin-managed); `TranslationApiResource` + restore defaults action.

## Source of Truth

- `app/Services/TextTranslator.php`
- `app/Models/TranslationApi.php`
- `app/Filament/Forms/RichEditor/FullEditorPlugin.php`
- `tests/Feature/TextTranslatorTest.php`, `TranslationApiTest.php`

## Important Edge Cases

- Auto source uses Vietnamese diacritic heuristic (`guessSource`).
- Same source/target language returns original text unchanged.
- External APIs are third-party; availability not guaranteed (operational concern).

## Related Files

- `app/Filament/Resources/TranslationApis/TranslationApiResource.php`

## Common Mistakes

- Expecting translation to populate all locale keys automatically (only inserts into current editor content).
- Adding driver without extending `TextTranslator::viaDriver` match arm.

## Verification

- Default providers (`TranslationApi::defaults()`) use **public HTTP endpoints** without API keys; credentials are not stored in the schema.
- Failover order is by DB `sort_order`; providers may rate-limit or block requests — handled as failed attempt → try next driver → `RuntimeException` if all fail (see tests with 429 responses).
