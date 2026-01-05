<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MenuResource\Pages;
use App\Filament\Resources\MenuResource\RelationManagers;
use App\Models\Menu;
use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MenuResource extends Resource
{
    protected static ?string $model = Menu::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getModelLabel(): string
    {
        return __('resources.menus.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('resources.menus.plural');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('language_id')
                    ->label(__('resources.languages.singular'))
                    ->relationship('language', 'name')
                    ->required(),
                TextInput::make('name')
                    ->label(__('fields.labels.name'))
                    ->required(),
                Select::make('type')
                    ->label(__('fields.type'))
                    ->options([
                        'header' => 'Header',
                        'footer' => 'Footer',
                    ])
                    ->required(),
                Repeater::make('items')
                    ->label(__('fields.labels.items'))
                    ->relationship('items')
                    ->orderColumn('sort_order')
                    ->schema([
                        Select::make('page')
                            ->label(__( 'resources.pages.singular' ))
                            ->relationship('page', 'name')
                            ->nullable()
                            ->required(),
                    ])
                    ->columns(2)
                    ->defaultItems(1)
                    ->columnSpanFull(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('language.name')
                    ->label(__( 'resources.languages.singular')),
                TextColumn::make('name')
                    ->label(__('fields.labels.name')),
                TextColumn::make('type')
                    ->label(__('fields.labels.type'))
                    ->badge(),
            ]);
    }


    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMenus::route('/'),
            'create' => Pages\CreateMenu::route('/create'),
            'edit' => Pages\EditMenu::route('/{record}/edit'),
        ];
    }
}
