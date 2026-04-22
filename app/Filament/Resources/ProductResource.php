<?php

namespace App\Filament\Resources;

use App\Filament\Resources\IngredientResource\Pages;
use App\Filament\Resources\IngredientResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'fluentui-food-chicken-leg-16-o';

    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): string
    {
        return __('navigation.information');
    }
    public static function getModelLabel(): string
    {
        return __('resources.product.singular');
    }

    public static function getNavigationLabel(): string
    {
        return __('resources.product.plural');
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label(__('fields.labels.name'))
                    ->required(),
                Select::make('measurement_type_id')
                    ->label(__('fields.labels.measurement_type'))
                    ->relationship('measurementType', 'name')
                    ->required(),
                Select::make('product_category_id')
                    ->label(__('fields.labels.category'))
                    ->relationship('productCategory', 'name')
                    ->required(),
            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('fields.labels.name')),
                TextColumn::make('measurementType.name')
                    ->label(__('fields.labels.measurement_type'))
                    ->badge(),
                TextColumn::make('productCategory.name')
                    ->label(__('fields.labels.category'))
                    ->badge(),
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
            'index' => Pages\ListIngredients::route('/'),
            'create' => Pages\CreateIngredient::route('/create'),
            'edit' => Pages\EditIngredient::route('/{record}/edit'),
        ];
    }
}
