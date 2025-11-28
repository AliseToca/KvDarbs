<?php

namespace App\Repositories;

use Waavi\Translation\Repositories\LanguageRepository as BaseLanguageRepository;

class LanguageRepository extends BaseLanguageRepository
{
    protected ?array $locales = null;

    public function locales(): array
    {
        if ($this->locales === null) {
            try {
                $this->locales = self::all()->pluck('locale', 'locale')->toArray();
            } catch (\Exception) {
                return [];
            }
        }
        return $this->locales;
    }
}
