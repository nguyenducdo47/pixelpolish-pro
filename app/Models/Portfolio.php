<?php

namespace App\Models;

use App\Enums\ContentProfile;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Portfolio extends Model
{
    protected $fillable = [
        'user_id',
        'slug',
        'is_published',
        'default_locale',
        'default_theme',
        'content_profile',
        'theme_id',
        'appearance',
        'seo_title',
        'seo_description',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope('current_user', function ($query): void {
            if (! Filament::isServing()) {
                return;
            }

            $user = auth()->user();

            if ($user) {
                $query->where('user_id', $user->id);
            }
        });
    }

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'appearance' => 'array',
            'content_profile' => ContentProfile::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function profile(): HasOne
    {
        return $this->hasOne(Profile::class);
    }

    public function socialLinks(): HasMany
    {
        return $this->hasMany(SocialLink::class)->orderBy('sort_order');
    }

    public function skillCategories(): HasMany
    {
        return $this->hasMany(SkillCategory::class)->orderBy('sort_order');
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class)->orderBy('sort_order');
    }

    public function education(): HasMany
    {
        return $this->hasMany(Education::class)->orderBy('sort_order');
    }

    public function spokenLanguages(): HasMany
    {
        return $this->hasMany(SpokenLanguage::class)->orderBy('sort_order');
    }

    public function principles(): HasMany
    {
        return $this->hasMany(Principle::class)->orderBy('sort_order');
    }

    public function cvSettings(): HasOne
    {
        return $this->hasOne(CvSetting::class);
    }

    public function theme(): BelongsTo
    {
        return $this->belongsTo(Theme::class);
    }

    public function themeCustomizations(): HasMany
    {
        return $this->hasMany(PortfolioTheme::class);
    }

    public function publicUrl(?string $locale = null): string
    {
        $locale ??= $this->default_locale;

        return route('portfolio.show', [
            'locale' => $locale,
            'username' => $this->slug,
        ]);
    }

    public function cvUrl(?string $locale = null): string
    {
        $locale ??= $this->default_locale;

        return route('portfolio.cv', [
            'locale' => $locale,
            'username' => $this->slug,
        ]);
    }
}
