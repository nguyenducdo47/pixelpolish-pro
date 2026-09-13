# Mail configuration

## Purpose

Let platform admins configure outbound mail (log vs SMTP) from the admin panel, persist settings in the database, and apply them at runtime for password reset and test emails.

## Business Rules

- **Admin only**: `ManageMail::canAccess()` requires `is_admin`, admin panel id, and **not** impersonating.
- Studio URL `/studio/manage-mail` returns **403** even for admin users (`MailSettingTest`).
- Non-admin hitting `/admin/manage-mail` is redirected to **`/studio/setup`** (via `AuthenticateAdmin`).
- **Singleton row**: `MailSetting::current()` uses `first()` or creates from `.env` / `config/mail.php` defaults.
- Supported mailers in UI: **`log`** and **`smtp`** only (other `config('mail.default')` values normalize to `log` on first create).
- SMTP fields required when mailer is `smtp`; optional **`smtps`** scheme (port 465).
- Password field: **blank on save keeps** stored encrypted secret (`dehydrated` only when filled).
- On save: DB update → `writeConfig()` → `Mail::purge()`.
- **Runtime apply**: `AppServiceProvider` hooks `mail.manager` resolving → `MailSetting::applyToConfig()` (loads DB row into runtime `config()`).
- Test send: applies **current form state** (including unsaved) via `previewFromForm()`, sends `MailSettingsTestMail` to **logged-in admin email**.

## Workflow

1. Admin opens `/admin/manage-mail` (registered on admin panel only in `AdminPanelProvider::$pages`).
2. Form filled from `valuesForForm()` — merges stored row with env defaults for blank fields.
3. Save persists → config applied for subsequent mail in same app lifecycle.
4. Send test uses form snapshot; failures surface exception message in Filament notification.

## Data

| Table | Columns |
|-------|---------|
| `mail_settings` | `mailer`, `scheme`, `host`, `port`, `username`, `password` (encrypted), `from_address`, `from_name` |

## Source of Truth

- `app/Filament/Pages/ManageMail.php`
- `app/Models/MailSetting.php` (`current`, `writeConfig`, `applyToConfig`, `defaultsFromConfig`)
- `app/Providers/AppServiceProvider.php` (mail.manager hook)
- `app/Mail/MailSettingsTestMail.php`
- `tests/Feature/MailSettingTest.php`

## Important Edge Cases

- If `mail_settings` table missing, `applyToConfig()` no-ops (try/catch).
- Switching mailer to `log` still saves from/reply metadata; SMTP host not cleared in DB but not applied when not smtp.
- Password reset uses Laravel mail stack after config apply — same pipeline as test mail.

## Related Files

- `config/mail.php` — baseline before DB row exists
- `database/migrations/2026_09_11_000006_create_mail_settings_table.php`

## Common Mistakes

- Expecting `.env` changes alone to override saved DB settings after first save (DB wins via `applyToConfig`).
- Testing SMTP only via `.env` without opening admin page on fresh install (row may mirror env until edited).
- Adding mail settings to Studio (must stay admin-only).

## Verification

- Covered by `tests/Feature/MailSettingTest.php` (access control, save, password retention, test send, config apply).
