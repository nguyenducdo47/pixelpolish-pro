# Authentication & accounts

## Purpose

Session-based login for Studio/Admin, self-service registration creating a portfolio, and password reset with localized notifications.

## Business Rules

- Registration open to guests (`RegisterController` + `RegisterRequest`).
- **Username** rules: `alpha_dash`, 3–40 chars, unique, reserved slugs blocked, stored lowercase.
- Password: Laravel `Password::defaults()` + confirmation.
- On register success: auto-login, session regenerate, redirect **`/studio/setup`** via `Inertia::location(Studio::home())`.
- Logout: POST `/logout`, auth middleware.
- Password reset uses custom `ResetPasswordNotification` with `UiLocale::current()`.
- Filament admin panel has built-in login route on `/admin`; Studio uses app login at `/login`.

## Workflow

```
Register → User + Portfolio created (observer) → login session → Studio
Forgot password → token email → reset form → new password
```

## Data

- `users`: `name`, `username`, `email`, `password`, `is_admin`
- Session keys: `ui_locale`, `impersonator_id` (admin feature)

## Source of Truth

- `app/Http/Controllers/Auth/*`
- `app/Http/Requests/Auth/*`
- `app/Observers/UserObserver.php` (portfolio bootstrap — see portfolio doc)
- `app/Notifications/ResetPasswordNotification.php`
- `tests/Feature/AuthTest.php`

## Important Edge Cases

- `UserObserver::creating` auto-generates username from name/email if missing (Filament admin create may rely on this).
- `email_verified_at` exists on `users` but **no** `MustVerifyEmail`, verified middleware, or route checks — verification is **not enforced**.

## Related Files

- `resources/js/Pages/Auth/Login.vue`, `Register.vue`
- `database/migrations/2026_09_11_000001_add_username_to_users_table.php`

## Common Mistakes

- Allowing usernames that collide with route segments (`register`, `studio`, …) — blocked at validation.
- Creating users only via DB without observer side effects (missing portfolio).

## Verification

- **Login** (`LoginController::store`): `redirect()->intended()` with default **`/admin`** if `isAdmin()`, else **`/studio/setup`** (`Studio::home()`).
- **Already authenticated guest** (`RedirectIfAuthenticated` in `AppServiceProvider`): same rule — admin → `/admin`, others → Studio home; impersonating admin goes to Studio.
