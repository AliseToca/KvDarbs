<?php

namespace App\Http\Middleware;

use Illuminate\Contracts\Encryption\Encrypter;
use Illuminate\Cookie\Middleware\EncryptCookies as Middleware;
use Symfony\Component\HttpFoundation\Request;

class EncryptCookies extends Middleware
{
    /**
     * The names of the cookies that should not be encrypted.
     *
     * @var array
     */
    protected $except = [
        'show_cookie_notice',
    ];

    public function __construct(Encrypter $encrypter, Request $request)
    {
        parent::__construct($encrypter);

        $this->except = array_merge($this->except, $this->getCookieGroups($request));
    }

    public function getCookieGroups(Request $request): array
    {
        $cookieGroups = $request->get('cookie_groups');

        return $cookieGroups ? $cookieGroups->pluck('name')->toArray() : [];
    }
}
