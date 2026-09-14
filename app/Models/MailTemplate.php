<?php

namespace App\Models;

use App\Enums\MailTemplateKind;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MailTemplate extends Model
{
    protected $fillable = [
        'slug',
        'name',
        'kind',
        'subject',
        'body_html',
        'body_text',
        'is_system',
    ];

    protected function casts(): array
    {
        return [
            'kind' => MailTemplateKind::class,
            'is_system' => 'boolean',
        ];
    }

    public function campaigns(): HasMany
    {
        return $this->hasMany(MailCampaign::class);
    }
}
