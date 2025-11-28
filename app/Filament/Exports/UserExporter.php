<?php

namespace App\Filament\Exports;

use App\Models\User;

class UserExporter extends BaseExporter
{
    protected static ?string $model = User::class;

    protected static array $excludedColumns = ['password', 'remember_token'];
}
