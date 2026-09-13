# Important relations

## ER (conceptual)

```
users 1──1 portfolios
portfolios 1──1 profiles
portfolios 1──1 cv_settings
portfolios 1──* social_links | skill_categories | projects | education | spoken_languages | principles
skill_categories 1──* skills
portfolios *──1 themes (theme_id, nullable)
portfolios *──* themes via portfolio_themes (customization per theme_id)
users (no FK) — admin flag only
locales — standalone config
translation_apis — standalone config
mail_settings — standalone config
```

## Cardinality rules

- Deleting **user** cascades **portfolio** and all children.
- Deleting **theme** referenced by portfolio: `theme_id` **nullOnDelete** (portfolio keeps row, assignment lost).
- **`portfolio_themes`**: unique `(portfolio_id, theme_id)` — one customization row per pair.

## Identity mapping

| Public URL | DB |
|------------|-----|
| `{username}` | `portfolios.slug` (= `users.username` when synced) |
| `{locale}` | `locales.code` (must be enabled) |

## Query scoping (runtime)

| Context | Portfolio rows visible |
|---------|------------------------|
| Filament Studio/Admin | Current auth user's portfolio only (`Portfolio` global scope) |
| Public controller | No scope — lookup by slug |
| Migrations/seeders | Use `withoutGlobalScopes()` |

## JSON fields (translatable)

`profiles`: headline, tagline, about, philosophy_quote  
`skill_categories`: name  
`skills`: description  
`projects`: title, subtitle, complexity, summary, problem, solution, learned, highlights (list per locale)  
`education`: degree, school, details  
`spoken_languages`: name, level  
`principles`: title, description  

Non-JSON: skill `name`, project `tech_stack`, URLs, periods, toggles.

## Indexes worth knowing

- Unique slugs/usernames — routing depends on index performance only; correctness is uniqueness.

## Related docs

- `docs/business/portfolio.md`
- `docs/business/i18n.md`
- `docs/business/appearance.md`
