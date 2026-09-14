# Mail campaigns (bulk email)

Admin-only bulk email: templates, audience filters, scheduled sends via queue, marketing unsubscribe.

## Related docs

- SMTP / transport: [mail.md](./mail.md)
- Admin panel access: same rules as **Manage mail** (admin, not impersonating)

## Data model

| Table | Purpose |
|-------|---------|
| `mail_templates` | Reusable HTML/text templates (`transactional` or `marketing`) |
| `mail_campaigns` | Named send: template, audience JSON, `send_at`, status counters |
| `mail_campaign_recipients` | Materialized rows per user/email |
| `user_mail_preferences` | `marketing_opted_in_at`, `marketing_unsubscribed_at`, signed `unsubscribe_token` |

## Marketing opt-in

Users must **opt in** (register checkbox or Studio **Profile → Account**) to receive marketing campaigns. Link unsubscribe in marketing mail sets `marketing_unsubscribed_at`; they can opt in again from Studio.

**Transactional** mail (e.g. password reset via Laravel notifications) is unrelated to `MailCampaign` and is always sent to the account email.

## Audience JSON

Stored on `mail_campaigns.audience`:

- `exclude_admins` (default true)
- `is_published` — `null` | `true` | `false` (portfolio)
- `content_profiles` — array of `ContentProfile` values
- `require_marketing_opt_in` — default true for marketing templates (only users with opt-in and not unsubscribed)

Resolver: `App\Services\MailCampaigns\CampaignAudienceResolver`.

## Placeholders

Replaced per recipient in subject/body (see admin **Email templates** form for descriptions):

| Tag | Purpose |
|-----|---------|
| `{{user.name}}` | Display name |
| `{{user.email}}` | Login email |
| `{{user.username}}` | Portfolio username |
| `{{portfolio.url}}` | Public site URL if published |
| `{{studio.url}}` | Studio login URL |
| `{{unsubscribe.url}}` | Marketing unsubscribe (signed); empty for transactional |

Renderer: `CampaignMailRenderer`.

## Send pipeline

1. Admin creates campaign (draft) in Filament → **Mail campaigns**.
2. **Build recipient list** materializes users into `mail_campaign_recipients`.
3. **Schedule** sets `status=scheduled` and `send_at`, or **Send now** sets `sending` and dispatches `ProcessMailCampaignJob`.
4. Scheduler runs `mail-campaigns:process-scheduled` every minute (`bootstrap/app.php`) for due scheduled campaigns.
5. `ProcessMailCampaignJob` queues `SendCampaignRecipientJob` per pending recipient (~2s stagger).
6. `SendCampaignRecipientJob` sends `CampaignMessageMail`, updates counts, and stores a copy in **Inbox** (`user_inbox_messages`) for the recipient in Studio/Admin.

Requires `QUEUE_CONNECTION=database` (or redis) and a running worker (`php artisan queue:work`).

## In-app inbox (Studio & Admin)

Campaign mail (and admin **Send test** on templates) also creates `UserInboxMessage` rows. Users open **Inbox** in the panel nav (badge + bell icon in the top bar when unread): view HTML body, mark read (single/bulk/all), delete (bulk/all).

Service: `App\Services\UserInboxService`. UI: `UserInboxMessageResource`.

## Unsubscribe

Signed GET `/mail/unsubscribe/{token}` → `MailUnsubscribeController` → Inertia `Mail/Unsubscribe`.

## Filament (admin panel)

- **Email templates** — `MailTemplateResource` (test send to current admin)
- **Mail campaigns** — `MailCampaignResource` (estimate, materialize, schedule, send now, cancel)

## Seed

`Database\Seeders\MailTemplateSeeder` — system templates `product-update`, `account-reminder`.

## Source of truth

| Area | Files |
|------|--------|
| Models | `MailTemplate`, `MailCampaign`, `MailCampaignRecipient`, `UserMailPreference` |
| Services | `MailCampaignService`, `CampaignAudienceResolver`, `CampaignMailRenderer` |
| Jobs | `ProcessMailCampaignJob`, `SendCampaignRecipientJob` |
| Command | `ProcessScheduledMailCampaigns` |
| HTTP | `MailUnsubscribeController`, route `mail.unsubscribe` |
