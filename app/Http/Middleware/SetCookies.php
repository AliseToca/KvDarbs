<?php

namespace App\Http\Middleware;

use App\Models\Cookies\CookieGroup;
use App\Services\CookiesService;
use Closure;
use Illuminate\Http\Request;

class SetCookies
{
    protected CookiesService $cookiesService;

    public function __construct(CookiesService $cookiesService)
    {
        $this->cookiesService = $cookiesService;
    }

    public function handle(Request $request, Closure $next): mixed
    {
        $cookieGroups = CookieGroup::where('active', 1)->get();

        $request->attributes->add(['cookie_groups' => $cookieGroups]);

        if ($cookieGroups->isEmpty()) {
            return $next($request);
        }

        foreach ($cookieGroups as $group) {
            if ($group->is_mandatory || $this->cookiesService->cookieGroupIsSet($request, $group)) {
                continue;
            }

            $this->cookiesService->setCookie($group);
            if (!$request->hasCookie(CookiesService::SHOW_COOKIE_NOTICE)) {
                $this->cookiesService->showCookieNotice();
            }
        }

        return $next($request);
    }
}
