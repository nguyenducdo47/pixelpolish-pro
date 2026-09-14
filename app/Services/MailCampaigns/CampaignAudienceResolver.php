<?php

namespace App\Services\MailCampaigns;

use App\Enums\MailTemplateKind;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class CampaignAudienceResolver
{
    /**
     * @param  array<string, mixed>  $audience
     */
    public function query(array $audience): Builder
    {
        $query = User::query()
            ->where('is_disabled', false)
            ->whereNull('deleted_at');

        if ($audience['exclude_admins'] ?? true) {
            $query->where('is_admin', false);
        }

        if (array_key_exists('is_published', $audience) && $audience['is_published'] !== null) {
            $published = (bool) $audience['is_published'];
            $query->whereHas('portfolio', fn (Builder $q) => $q->where('is_published', $published));
        }

        $profiles = $audience['content_profiles'] ?? [];

        if (is_array($profiles) && $profiles !== []) {
            $query->whereHas('portfolio', fn (Builder $q) => $q->whereIn('content_profile', $profiles));
        }

        if ($audience['require_marketing_opt_in'] ?? false) {
            $query->whereHas('mailPreference', fn (Builder $p) => $p
                ->whereNotNull('marketing_opted_in_at')
                ->whereNull('marketing_unsubscribed_at'));
        }

        return $query->orderBy('id');
    }

    /**
     * @param  array<string, mixed>  $audience
     */
    public function count(array $audience): int
    {
        return $this->query($audience)->count();
    }

    /**
     * @return array<string, mixed>
     */
    public function defaults(MailTemplateKind $kind): array
    {
        return [
            'exclude_admins' => true,
            'is_published' => null,
            'content_profiles' => [],
            'require_marketing_opt_in' => $kind === MailTemplateKind::Marketing,
        ];
    }
}
