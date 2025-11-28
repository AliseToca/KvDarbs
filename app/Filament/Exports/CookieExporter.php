<?php

namespace App\Filament\Exports;

use App\Models\Cookies\Cookie;

class CookieExporter extends BaseExporter
{
    protected static ?string $model = Cookie::class;
}
