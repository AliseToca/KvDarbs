<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PageResource\CreatePage;
use App\Filament\Resources\PageResource\EditPage;
use App\Models\Page;
use CubeAgency\FilamentPageManager\Filament\Resources\PageResource\Pages\ListPages;
use Filament\Resources\Resource;

class PageResource extends Resource
{
    protected static ?string $model = Page::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getPages(): array
    {
        return [
            'index' => ListPages::route('/'),
            'create' => CreatePage::route('/create'),
            'edit' => EditPage::route('/{record}/edit'),
        ];
    }
}
