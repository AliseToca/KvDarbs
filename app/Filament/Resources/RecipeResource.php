<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RecipeResource\Pages;
use App\Filament\Resources\RecipeResource\RelationManagers;
use App\Models\Recipe;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Filament\Forms;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RecipeResource extends Resource
{
    protected static ?string $model = Recipe::class;

    protected static ?string $navigationIcon = 'fluentui-receipt-24-o';

    public static function getModelLabel(): string
    {
        return __('resources.recipes.singular');
    }

    public static function getNavigationLabel(): string
    {
        return __('resources.recipes.plural');
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label(__('fields.labels.name'))
                    ->required(),
                //TO DO check cube agency if has slug comp
                TextInput::make('slug')
                    ->label(__('fields.labels.slug'))
                    ->required(),
                CuratorPicker::make('image')
                    ->label(__('fields.labels.image')),
                TextArea::make('content')
                    ->label(__('fields.labels.content'))
                    ->required(),
                TextInput::make('time_needed_minutes')
                    ->label(__('fields.labels.cook_time')),
                TextInput::make('servings')
                    ->label(__('fields.labels.servings'))
                    ->integer(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('fields.labels.name')),
                TextColumn::make('created_at')
                    ->label(__('fields.labels.created_at'))
                    ->since(),
                //TO DO add author, rating, review count,
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
            'index' => Pages\ListRecipes::route('/'),
            'create' => Pages\CreateRecipe::route('/create'),
            'edit' => Pages\EditRecipe::route('/{record}/edit'),
        ];
    }
}
