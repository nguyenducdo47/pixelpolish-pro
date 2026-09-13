# Workflow: Admin impersonation

## Preconditions

- Actor is admin (`is_admin`).
- Not already impersonating (`session` lacks `impersonator_id`).

## Enter

1. Admin → `/admin` → Users → **Edit portfolio** action.
2. GET `/impersonation/enter/{user}` (`ImpersonationController::enter`).
3. Store `impersonator_id = admin id`.
4. `Auth::login($targetUser)`.
5. Redirect `/studio/setup`.

## While impersonating

- Studio shows banner (`impersonation-banner` render hook).
- Admin panel **inaccessible** for impersonated user model (`canAccessPanel` admin check).
- Public preview works as target owner.
- Portfolio global scopes use target user's `portfolio_id`.

## Leave

1. GET `/impersonation/leave` (auth as target).
2. Restore session user to `impersonator_id`.
3. Forget impersonator key.
4. Redirect `/admin/users`.

## Constraints

- Cannot impersonate self (redirect Studio home).
- Cannot nest impersonation.

## Key files

- `app/Http/Controllers/ImpersonationController.php`
- `app/Filament/Resources/Users/UserResource.php` (impersonate action)
- `app/Models/User.php` (`canAccessPanel`)
