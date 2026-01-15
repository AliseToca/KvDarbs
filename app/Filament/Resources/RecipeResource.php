<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RecipeResource\Pages;
use App\Models\Recipe;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;

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
                Section::make()
                    ->schema([
                        TextInput::make('name')
                            ->label(__('fields.labels.name'))
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Get $get, Set $set, ?string $old, ?string $state) {
                                if (($get('slug') ?? '') !== Str::slug($old)) {
                                    return;
                                }

                                $set('slug', Str::slug($state));
                            }),
                        TextInput::make('slug')
                            ->label(__('fields.labels.slug'))
                            ->prefix(function ($record) {
                                return '';
                            })
                            ->required()
                            ->unique(ignoreRecord: true),
                        FileUpload::make('image_src')
                            ->directory('recipes')
                            ->image()
                            ->imageCropAspectRatio('16:9')
                            ->imageEditor()
                            ->label(__('fields.labels.image')),
                        //TO DO decide is content needed
                        RichEditor::make('content')
                            ->label(__('fields.labels.content'))
                            ->columnSpanFull()
                            ->required(),
                        Fieldset::make('Cooking information')
                            ->columns(3)
                            ->schema([
                                TextInput::make('prep_time')
                                    ->label(__('fields.labels.recipe.prep_time'))
                                    ->integer()
                                    ->default(0)
                                    ->required(),
                                TextInput::make('cook_time')
                                    ->label(__('fields.labels.recipe.cook_time'))
                                    ->integer()
                                    ->default(0)
                                    ->required(),
                                TextInput::make('servings')
                                    ->label(__('fields.labels.recipe.servings'))
                                    ->integer()
                                    ->default(1)
                                    ->required(),
                                Repeater::make('ingredients')
                                    ->relationship()
                                    ->label(__('fields.labels.recipe.ingredients'))
                                    ->schema([
                                        Select::make('product_id')
                                            ->label('Ingredient')
                                            ->relationship('product', 'name')
                                            ->preload()
                                            ->searchable()
                                            ->required()
                                            ->columnSpan(6),
                                        TextInput::make('amount')
                                            ->numeric()
                                            ->required()
                                            ->columnSpan(3),
                                        Select::make('unit_id')
                                            ->label('Unit')
                                            ->relationship('unit', 'name')
                                            ->required()
                                            ->columnSpan(3),
                                    ])
                                    ->columns(12)
                                    ->reorderable()
                                    ->defaultItems(1)
                                    ->columnSpanFull(),
                                Repeater::make('instructions')
                                    ->label(__('fields.labels.recipe.instructions'))
                                    ->schema([
                                        Textarea::make('text')
                                            ->label(__('fields.labels.description'))
                                            ->required()
                                            ->rows(3),
                                    ])
                                    ->itemLabel(fn (array $state, $uuid, $component): string => __('fields.labels.recipe.step') . ' ' . (array_search($uuid, array_keys($component->getState())) + 1))
                                    ->defaultItems(1)
                                    ->reorderable()
                                    ->columnSpanFull(),
                            ]),
                    ]),
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
