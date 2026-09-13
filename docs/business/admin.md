# Admin panel & impersonation

## Purpose

Filament panel at **`/admin`** for platform operators: manage users, locales, theme catalog, translation API providers, mail settings; impersonate users to edit their portfolios in Studio.

## Business Rules

### Admin access

- Requires `users.is_admin = true`.
- **`canAccessPanel('admin')` false** while impersonating (`impersonator_id` in session) — admin panel blocked for impersonated session.
- Non-admin attempting `/admin` authenticated → redirected to **`/studio/setup`** (`AuthenticateAdmin`).

### Users

- `UserResource` only on **admin** panel (`Filament::getCurrentPanel()->getId() === 'admin'`).
- Cannot delete self; cannot demote **last** admin (`cannotDemoteAdmin`).
- Editing user updates linked portfolio: `slug`, `is_published`, `default_locale`.
- **Impersonate** action logs admin in as target user, stores `impersonator_id`, redirects Studio home.
- Nested impersonation blocked (`enter` aborts if already impersonating).

### Impersonation leave

- Restores admin session by id from session; redirects `/admin/users`.

### Platform settings (admin resources)

| Resource | Purpose |
|----------|---------|
| Locales | Enable/disable languages, default flag |
| Themes | Global theme catalog |
| TranslationApis | External translator endpoints order/drivers |
| ManageMail | SMTP/settings test mail — see [mail.md](mail.md) |

### Bootstrap admin

- Migration ensures at least one admin if column exists and none present (first user by id).

## Workflow

```
Admin → Users → Impersonate → Studio as user → edit → Leave impersonation → Admin users list
```

## Data

Same tables as Studio; admin mutations affect global config (`locales`, `themes`, `translation_apis`, `mail_settings`).

## Source of Truth

- `app/Providers/Filament/AdminPanelProvider.php`
- `app/Filament/Resources/Users/UserResource.php`
- `app/Http/Controllers/ImpersonationController.php`
- `app/Models/User.php` (`canAccessPanel`, `isAdmin`)
- `app/Filament/Pages/ManageMail.php`

## Important Edge Cases

- Admin viewing unpublished portfolio on public site works via `canPreview` without impersonation.
- Clear cache available in both panels (auth required POST `/cache/clear`).

## Related Files

- `database/migrations/2026_09_11_000005_ensure_at_least_one_admin_user.php`
- `resources/views/filament/impersonation-banner.blade.php`

## Common Mistakes

- Expecting UserResource in Studio nav (intentionally hidden).
- Impersonating while needing admin panel (must leave first).
- Removing last admin via `is_admin` toggle (UI disables demote for last admin).

## Verification

- `ManageMail` is listed in **`AdminPanelProvider::$pages`** only; `canAccess()` requires admin panel + admin user + no impersonation. Confirmed: `/studio/manage-mail` → 403, regular user `/admin/manage-mail` → redirect Studio (`MailSettingTest`).
