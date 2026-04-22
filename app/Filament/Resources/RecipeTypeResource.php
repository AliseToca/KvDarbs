<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RecipeTypeResource\Pages;
use App\Filament\Resources\RecipeTypeResource\RelationManagers;
use App\Models\RecipeType;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RecipeTypeResource extends Resource
{
    protected static ?string $model = RecipeType::class;

    protected static ?string $navigationIcon = 'fluentui-tag-20-o';

    protected static ?int $navigationSort = 3;

    public static function getNavigationGroup(): string
    {
        return __('navigation.information');
    }

    public static function getModelLabel(): string
    {
        return __('resources.recipe_types.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('resources.recipe_types.plural');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label(__('fields.labels.name'))
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('fields.labels.name')),
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
            'index' => Pages\ListRecipeTypes::route('/'),
            'create' => Pages\CreateRecipeType::route('/create'),
            'edit' => Pages\EditRecipeType::route('/{record}/edit'),
        ];
    }
}
