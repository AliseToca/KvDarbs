<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RecipeCategoryResource\Pages;
use App\Filament\Resources\RecipeCategoryResource\RelationManagers;
use App\Models\RecipeCategory;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RecipeCategoryResource extends Resource
{
    protected static ?string $model = RecipeCategory::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    public static function getModelLabel(): string
    {
        return __('resources.recipe_categories.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('resources.recipe_categories.plural');
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
            'index' => Pages\ListRecipeCategories::route('/'),
            'create' => Pages\CreateRecipeCategory::route('/create'),
            'edit' => Pages\EditRecipeCategory::route('/{record}/edit'),
        ];
    }
}
