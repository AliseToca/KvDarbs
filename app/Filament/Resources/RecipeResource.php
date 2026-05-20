<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RecipeResource\Pages;
use App\Models\Recipe;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use App\Forms\Components\DurationPicker;
use App\Enums\Recipe\Visibility;

class RecipeResource extends Resource
{
    protected static ?string $model = Recipe::class;

    protected static ?string $navigationIcon = 'fluentui-receipt-24-o';

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): string
    {
        return __('navigation.information');
    }

    public static function getModelLabel(): string
    {
        return __('resources.recipes.singular');
    }

    public static function getNavigationLabel(): string
    {
        return __('resources.recipes.plural');
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()
            ->withCount('reviews')
            ->withAvg('reviews', 'rating');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        TextInput::make('name')
                            ->label(__('fields.labels.name'))
                            ->required(),

                        FileUpload::make('image_src')
                            ->directory('recipes')
                            ->image()
                            ->imageCropAspectRatio('16:9')
                            ->imageEditor()
                            ->label(__('fields.labels.image')),

                        Select::make('visibility')
                            ->label(__('fields.labels.recipe.visibility'))
                            ->options(Visibility::class)
                            ->required(),
                    ]),

                Section::make(__('recipe.details'))
                    ->schema([
                        Fieldset::make()
                            ->columns(3)
                            ->schema([
                                DurationPicker::make('prep_time')
                                    ->label(__('fields.labels.recipe.prep_time')),
                                DurationPicker::make('cook_time')
                                    ->label(__('fields.labels.recipe.cook_time')),
                                TextInput::make('servings')
                                    ->label(__('fields.labels.recipe.servings'))
                                    ->integer()
                                    ->default(1)
                                    ->required(),
                            ]),

                        Select::make('recipe_type_id')
                            ->label(__('resources.recipe_types.singular'))
                            ->relationship('recipeType', 'name')
                            ->preload()
                            ->searchable(),

                        Select::make('recipeCategories')
                            ->label(__('resources.recipe_categories.plural'))
                            ->relationship('recipeCategories', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable(),
                    ]),

                Section::make(__('recipe.ingredients'))
                    ->schema([
                        Repeater::make('recipeProducts')
                            ->label(__('resources.recipe_products.plural'))
                            ->relationship()
                            ->schema([
                                Select::make('product_id')
                                    ->label(__('fields.labels.name'))
                                    ->relationship('product', 'name')
                                    ->preload()
                                    ->searchable()
                                    ->required()
                                    ->columnSpan(9),
                                TextInput::make('amount')
                                    ->label(__('fields.labels.product.amount'))
                                    ->numeric()
                                    ->required()
                                    ->columnSpan(3)
                                    ->helperText(__('fields.labels.product.amount_helper')),
                            ])
                            ->columns(12)
                            ->reorderable()
                            ->defaultItems(1),
                    ]),

                Section::make(__('recipe.instructions'))
                    ->schema([
                        Repeater::make('instructions')
                            ->label(__('fields.labels.recipe.instructions'))
                            ->simple(
                                Textarea::make('instruction')
                                    ->required()
                                    ->rows(3),
                            )
                            ->defaultItems(1)
                            ->reorderable(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('fields.labels.name'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('user.username')
                    ->label(__('fields.labels.author'))
                    ->sortable(),

                TextColumn::make('recipeType.name')
                    ->label('Ēdienreize')
                    ->badge(),

                TextColumn::make('visibility')
                    ->label(__('fields.labels.recipe.visibility'))
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        \App\Enums\Recipe\Visibility::Public    => 'success',
                        \App\Enums\Recipe\Visibility::Private   => 'danger',
                        \App\Enums\Recipe\Visibility::Household => 'warning',
                        default => 'gray',
                    }),

                TextColumn::make('servings')
                    ->label(__('fields.labels.recipe.servings'))
                    ->sortable(),

                TextColumn::make('reviews_count')
                    ->label(__('fields.labels.recipe.reviews'))
                    ->sortable(),

                TextColumn::make('reviews_avg_rating')
                    ->label(__('fields.labels.recipe.rating'))
                    ->sortable()
                    ->formatStateUsing(fn ($state) => $state ? number_format($state, 1) : '-'),

                TextColumn::make('created_at')
                    ->label(__('fields.labels.created_at'))
                    ->since()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('visibility')
                    ->options(\App\Enums\Recipe\Visibility::class),

                Tables\Filters\SelectFilter::make('recipeType')
                    ->relationship('recipeType', 'name'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
