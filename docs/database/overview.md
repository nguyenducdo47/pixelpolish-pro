# Database overview

## Engine

MySQL (local Laragon / Docker compose). SQLite possible for skeleton but project README assumes MySQL.

## Domain groups

| Group | Tables |
|-------|--------|
| Auth | `users`, `password_reset_tokens`, `sessions` (Laravel default migration) |
| Portfolio core | `portfolios`, `profiles`, `cv_settings` |
| Portfolio sections | `social_links`, `skill_categories`, `skills`, `projects`, `education`, `spoken_languages`, `principles` |
| i18n config | `locales` |
| Appearance | `themes`, `portfolio_themes` |
| Platform | `translation_apis`, `mail_settings` |
| Infra | `cache`, `jobs` |

## Conventions

- **Cascade delete** from portfolio to all child content rows.
- **Unique** `portfolios.user_id`, `portfolios.slug`, `profiles.portfolio_id`, `cv_settings.portfolio_id`.
- Localized content: **JSON** columns keyed by locale code.
- Ordering: `sort_order` unsigned integer, default 0.

## Migrations order (custom)

1. Users + username + admin flag
2. Portfolio tables bundle
3. Locales
4. Mail settings
5. Translation APIs
6. Appearance JSON column (legacy)
7. Themes + portfolio_themes + theme_id FK + data migration from appearance JSON

## Seeders

- `LocaleSeeder` — `vi` (default), `en`
- `DemoPortfolioSeeder` — demo portfolio user/content (see seeder for slug/email)

## Source of Truth

- `database/migrations/*`
- `database/seeders/DatabaseSeeder.php`
