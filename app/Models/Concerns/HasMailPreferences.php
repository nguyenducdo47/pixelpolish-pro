<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Relations\HasOne;

trait HasMailPreferences
{
    public function mailPreference(): HasOne
    {
        return $this->hasOne(UserMailPreference::class);
    }
}
