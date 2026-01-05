<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CookieResource\Pages;
use App\Filament\Tables\Actions\ResourceExportAction;
use App\Models\Cookies\Cookie;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use SolutionForest\FilamentTranslateField\Forms\Component\Translate;

class CookieResource extends Resource
{
    protected static ?string $model = Cookie::class;

    protected static ?string $navigationIcon = 'iconoir-half-cookie';

    protected static ?string $navigationGroup = 'Settings';

    public static function getModelLabel(): string
    {
        return __('resources.cookies.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('resources.cookies.plural');
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Translate::make()
                    ->schema(fn(string $locale) => [
                        TextInput::make('expiration')->rules('required'),
                        Textarea::make('purpose')->rules('required'),
                    ])
                    ->columnSpanFull()
                    ->columns(),
                Select::make('cookie_group_id')
                    ->relationship(name: 'cookieGroup', titleAttribute: 'title')
                    ->label('Cookie Group')
                    ->rules('required'),
                TextInput::make('title')->rules('required'),
                TextInput::make('provider')->rules('required'),
                TextInput::make('type')->rules('required'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('cookieGroup.title'),
                TextColumn::make('title'),
                TextColumn::make('provider'),
                TextColumn::make('expiration'),
                TextColumn::make('type'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->headerActions([
                ResourceExportAction::make()->model(static::$model)->setDefaultExporter(),
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
            'index' => Pages\ListCookie::route('/'),
            'create' => Pages\CreateCookie::route('/create'),
            'edit' => Pages\EditCookie::route('/{record}/edit'),
        ];
    }
}
