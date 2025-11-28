<?php

namespace App\Filament\Resources\CookieGroupResource\Pages;

use App\Filament\Resources\CookieGroupResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCookieGroup extends EditRecord
{
    protected static string $resource = CookieGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
