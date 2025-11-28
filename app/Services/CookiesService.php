<?php

namespace App\Services;

use App\Filament\Templates\CookiesTemplate;
use App\Models\Cookies\CookieGroup;
use CubeAgency\FilamentPageManager\Models\Page;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class CookiesService
{
    public const SHOW_COOKIE_NOTICE = 'show_cookie_notice';
    public const MINUTES_IN_HOUR = 60;
    public const HOURS_IN_DAY = 24;
    public const DAYS_IN_YEAR = 365;
    public const MINUTES_IN_DAY = self::MINUTES_IN_HOUR * self::HOURS_IN_DAY;
    public const MINUTES_IN_YEAR = self::MINUTES_IN_HOUR * self::HOURS_IN_DAY * self::DAYS_IN_YEAR;

    public static function isCookieNoticeActive(): bool
    {
        $value = request()->cookie(self::SHOW_COOKIE_NOTICE);
        return !request()->hasCookie(self::SHOW_COOKIE_NOTICE) || $value === null || $value == 1;
    }

    public static function getCookieGroups(): Collection
    {
        $cookieGroups = CookieGroup::where('active', 1)->get();

        if ($cookieGroups->count() > 0) {
            foreach ($cookieGroups as $key => $item) {
                $value = request()->cookie($item['name']);
                $cookieGroups[$key]['checked'] = $value === null ? false : $value;
            }
        }

        return $cookieGroups->sortBy('position');
    }

    public function cookieGroupIsSet(Request $request, CookieGroup $group): bool
    {
        return $request->hasCookie($group->name) && !cookie()->queued($group->name);
    }

    public function setCookie(CookieGroup $group): void
    {
        $value = $group->enabled_by_default ?? 0;

        cookie()->queue(
            cookie(
                $group->name,
                (string)$value,
                self::MINUTES_IN_DAY,
                '/',
                null,
                config('app.secure_cookie'),
                true
            )
        );
    }

    public function showCookieNotice(): void
    {
        cookie()->queue(cookie()->forever(self::SHOW_COOKIE_NOTICE, '1', '/'));
    }

    public function acceptAllCookies(): void
    {
        $cookieGroups = $this->getCookieGroups();

        foreach ($cookieGroups as $key => $item) {
            if ($item['is_mandatory']) {
                continue;
            }

            cookie()->queue(
                cookie(
                    $item['name'],
                    '1',
                    self::MINUTES_IN_YEAR,
                    '/',
                    null,
                    config('app.secure_cookie'),
                    true
                )
            );
        }

        $this->hideCookieNotice();
    }

    public function saveSelectedCookies(Request $request): void
    {
        $cookieGroups = $this->getCookieGroups();

        foreach ($cookieGroups as $key => $item) {
            if ($item['is_mandatory']) {
                continue;
            }

            $value = (int)$request->get($item['name'], 0);
            $expiry = $value == 1 ? self::MINUTES_IN_YEAR : self::MINUTES_IN_DAY;
            cookie()->queue(
                cookie(
                    $item['name'],
                    (string)$value,
                    $expiry,
                    '/',
                    null,
                    config('app.secure_cookie'),
                    true
                )
            );
        }

        $this->hideCookieNotice();
    }

    public function rejectAllCookies(): void
    {
        $cookieGroups = $this->getCookieGroups();

        foreach ($cookieGroups as $key => $item) {
            if ($item['is_mandatory']) {
                continue;
            }

            cookie()->queue(
                cookie(
                    $item['name'],
                    '0',
                    self::MINUTES_IN_DAY,
                    '/',
                    null,
                    config('app.secure_cookie'),
                    true
                )
            );
        }

        $this->hideCookieNotice();
    }

    public function hideCookieNotice(): void
    {
        cookie()->queue(
            cookie()->forget(self::SHOW_COOKIE_NOTICE)
        );
    }

    public function getCookiesPage(): ?Page
    {
        return resolve(PagesService::class)->getPageByTemplate(CookiesTemplate::class);
    }
}
