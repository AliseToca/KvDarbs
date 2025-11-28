<?php

namespace App\Http\Controllers;

use App\Services\CookiesService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CookiesPageController extends ConstructorPageController
{
    public function __construct(protected CookiesService $cookiesService)
    {
    }

    public function acceptAllCookies(): RedirectResponse
    {
        $this->cookiesService->acceptAllCookies();

        return back();
    }

    public function saveSelectedCookies(Request $request): RedirectResponse
    {
        $this->cookiesService->saveSelectedCookies($request);

        return back();
    }

    public function rejectAllCookies(): RedirectResponse
    {
        $this->cookiesService->rejectAllCookies();

        return back();
    }
}
