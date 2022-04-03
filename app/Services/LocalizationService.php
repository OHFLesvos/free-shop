<?php

namespace App\Services;

use Illuminate\Support\Collection;

class LocalizationService
{
    private Collection $languages;

    public function __construct()
    {
        $this->languages = collect(config('localization.languages'));
    }

    public function isRtlLocale(): bool
    {
        return $this->isRtl(app()->getLocale());
    }

    public function isRtl(?string $languageCode): bool
    {
        return $this->languages->contains(fn ($language) => $languageCode == $language['code'] && $language['rtl'] === true);
    }
}
