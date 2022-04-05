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

    public function getLanguageCollection(): Collection
    {
        return $this->languages;
    }

    public function getLanguageName(string $locale): string
    {
        return $this->languages->firstWhere('code', $locale)['name'] ?? strtoupper($locale);
    }

    public function hasLanguageCode(string $locale): bool
    {
        return $this->languages->where('code', $locale)->isNotEmpty();
    }

    public function getLanguageCodes(): array
    {
        return $this->languages->pluck('code')->toArray();
    }

    public function getLanguageNames(): array
    {
        return $this->languages->pluck('name', 'code')->toArray();
    }

    public function getLocalizedNames(bool $publicOnly = false): array
    {
        return $this->languages
            ->when($publicOnly, fn(Collection $c) => $c->where('public', true))
            ->pluck('name_localized', 'code')
            ->toArray();
    }

    public function isRtlLocale(): bool
    {
        return $this->isRtl(app()->getLocale());
    }

    public function isRtl(?string $locale): bool
    {
        return $this->languages->contains(fn ($language) => $locale == $language['code'] && $language['rtl'] === true);
    }
}
