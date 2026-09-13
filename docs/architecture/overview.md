# Architecture Overview

## Product

**Portfotilo** — multi-user portfolio builder: owners edit content in Filament **Studio** (`/studio`); visitors view Inertia/Vue public pages and CV (HTML + PDF).

## Stack

| Layer | Technology |
|-------|------------|
| Backend | Laravel 13, PHP 8.3 |
| Public UI | Inertia.js + Vue 3 + Vite + Tailwind 4 |
| Admin / Studio | Filament 5 (two panels) |
| PDF | barryvdh/laravel-dompdf |
| Legacy (reference only) | `_legacy-react/` — not served by Laravel routes |

## Entry points

| Surface | Path / route | Role |
|---------|--------------|------|
| Landing | `GET /` | Marketing; links demo portfolio if `slug=nguyenducdo` published |
| Public portfolio | `GET /{locale}/{username}` | Inertia `Portfolio/Show` |
| Public CV | `GET /{locale}/{username}/cv`, `.../cv.pdf` | Inertia CV + DomPDF |
| Auth | `/login`, `/register`, password reset | Session auth; register creates user + portfolio |
| Studio | `/studio/*` | All authenticated users; home `/studio/setup` |
| Admin | `/admin/*` | `is_admin` only; login at panel |
| Impersonation | `/impersonation/enter/{user}`, `/leave` | Admin → act as user in Studio |

## Tenancy model

- **One user → one portfolio** (`users.id` ↔ `portfolios.user_id` unique).
- Public URL username = `portfolios.slug`, kept in sync with `users.username`.
- Portfolio-owned rows (projects, skills, …) use `portfolio_id`; Filament scopes queries to the logged-in user's portfolio.

## Two “locale” concepts

1. **Public content locale** — URL segment `{locale}`; drives `HasLocaleText` resolution on read via `PortfolioPresenter` + `SetLocale` middleware.
2. **UI locale** — Filament/panel strings + `LocaleTabs` editing context; `UiLocale` (session `ui_locale`, fallback `vi`).

Do not conflate them: changing UI language in Studio does not change the public URL locale.

## Two “theme” concepts

1. **`portfolios.default_theme`** — `system` \| `light` \| `dark` only; passed to Vue as prop `theme` (color mode).
2. **Appearance / catalog theme** — layout, colors, hero, CV layout; resolved by `AppearanceTheme` from `themes` + `portfolio_themes` + `portfolios.theme_id`.

## Authorization summary

| Actor | Public unpublished portfolio | Admin panel | Studio |
|-------|------------------------------|-------------|--------|
| Guest | 404 | — | — |
| Owner | Preview (200) | Redirect to Studio if not admin | Full access |
| Admin (not impersonating) | Preview | Full | Full |
| Admin impersonating | Preview as user | **Blocked** (`canAccessPanel`) | As target user |

## Request flow (public)

```
Browser → web.php → SetLocale → PortfolioController
       → Portfolio (by slug) → publish/preview check
       → PortfolioPresenter::publicPayload → Inertia page
```

Filament/Livewire routes skip `HandleInertiaRequests` middleware wrapping (see `HandleInertiaRequests::handle`).
