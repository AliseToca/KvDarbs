<?php

namespace App\Filament\Exports;
use App\Models\Cookies\CookieGroup;

class CookieGroupExporter extends BaseExporter
{
    protected static ?string $model = CookieGroup::class;
}
