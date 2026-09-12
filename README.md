# Portfotilo

Laravel 13 + Inertia Vue 3 + Filament. Multi-user portfolio builder, multilingual (VI/EN), dark mode, CV from portfolio data.

## Setup (Laragon, PHP 8.3)

```bash
composer install --prefer-dist
cp .env.example .env
php artisan key:generate
```

`.env`:

```
APP_URL=http://portfotilo.test
DB_CONNECTION=mysql
DB_DATABASE=portfotilo
DB_USERNAME=root
DB_PASSWORD=
APP_LOCALE=vi
```

```bash
php artisan migrate --seed
php artisan storage:link
npm install
npm run dev
```

## Accounts

- Demo: `ducdonguyen.dev@gmail.com` / `password`
- Public: `/vi/nguyenducdo` and `/en/nguyenducdo`
- CV: `/vi/nguyenducdo/cv` — PDF: `/vi/nguyenducdo/cv.pdf`
- Admin: `/admin` (register to create your own portfolio)

Use PHP 8.3, not 7.4.

## Setup (Docker)

Requires Docker Desktop / Docker Engine + Compose.

```bash
docker compose up --build
```

App: http://localhost:8080  
MySQL on the host: `127.0.0.1:3307` (user `portfotilo` / `portfotilo`)

Compose overrides `APP_URL` and `DB_*` so your Laragon `.env` can stay as-is. First boot runs `composer install`, `npm run build`, `migrate`, and seed.

```bash
# optional Vite HMR
docker compose --profile dev up --build

# shell
docker compose exec app php artisan tinker
docker compose exec app php artisan test
```

Accounts are the same as local setup. Stop with `docker compose down`. Add `-v` only if you also want to wipe the MySQL volume.
