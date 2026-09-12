<?php

namespace App\Models;

use App\Support\HasLocaleText;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class Profile extends Model
{
    use Concerns\BelongsToPortfolio;
    use HasLocaleText;

    protected $fillable = [
        'portfolio_id',
        'full_name',
        'email',
        'phone',
        'location',
        'website',
        'avatar_path',
        'date_of_birth',
        'headline',
        'tagline',
        'about',
        'philosophy_quote',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'headline' => 'array',
            'tagline' => 'array',
            'about' => 'array',
            'philosophy_quote' => 'array',
        ];
    }

    public function portfolio(): BelongsTo
    {
        return $this->belongsTo(Portfolio::class);
    }

    public function avatarUrl(): ?string
    {
        $path = $this->resolvedAvatarPath();

        if ($path === null || ! Storage::disk('public')->exists($path)) {
            return null;
        }

        return '/storage/'.ltrim(str_replace('\\', '/', $path), '/');
    }

    public function hasAvatar(): bool
    {
        return $this->avatarUrl() !== null;
    }

    public static function storedAvatarPath(mixed $value): ?string
    {
        if ($value instanceof TemporaryUploadedFile) {
            return $value->exists()
                ? ($value->store('avatars', 'public') ?: null)
                : null;
        }

        if (is_array($value)) {
            return self::storedAvatarPath($value[0] ?? null);
        }

        if (! is_string($value)) {
            return null;
        }

        $value = trim($value);

        if ($value === '' || $value === '[]' || $value === '{}' || $value === 'null') {
            return null;
        }

        return $value;
    }

    public function resolvedAvatarPath(): ?string
    {
        return self::storedAvatarPath($this->avatar_path);
    }

    protected function setAvatarPathAttribute(mixed $value): void
    {
        $this->attributes['avatar_path'] = self::storedAvatarPath($value);
    }
}
