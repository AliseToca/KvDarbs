<?php

namespace App\Filament\Resources;

use App\Enums\Cookies\CookieGroupName;
use App\Filament\Resources\CookieGroupResource\Pages;
use App\Filament\Tables\Actions\ResourceExportAction;
use App\Models\Cookies\CookieGroup;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use SolutionForest\FilamentTranslateField\Forms\Component\Translate;

class CookieGroupResource extends Resource
{
    protected static ?string $model = CookieGroup::class;

    protected static ?string $navigationIcon = 'iconoir-cookie';

    protected static ?string $navigationGroup = 'Settings';

    public static function getModelLabel(): string
    {
        return __('resources.cookie_groups.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('resources.cookie_groups.plural');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Translate::make()
                    ->schema(fn(string $locale) => [
                        TextInput::make('title'),
                        TextInput::make('description')->rules('required'),
                    ])
                    ->columnSpanFull()
                    ->columns(),
                Select::make('name')
                    ->options(CookieGroupName::asSelectArray())
                    ->rules('required', 'unique:' . CookieGroup::class . ",name,{$form->getModelInstance()->id}"),
                TextInput::make('position'),
                Toggle::make('active'),
                Toggle::make('enabled_by_default'),
                Toggle::make('is_mandatory'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('title'),
                TextColumn::make('description'),
                TextColumn::make('position'),
                IconColumn::make('active')->boolean(),
                IconColumn::make('enabled_by_default')->boolean(),
                IconColumn::make('is_mandatory')->boolean(),
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
            'index' => Pages\ListCookieGroups::route('/'),
            'create' => Pages\CreateCookieGroup::route('/create'),
            'edit' => Pages\EditCookieGroup::route('/{record}/edit'),
        ];
    }
}
