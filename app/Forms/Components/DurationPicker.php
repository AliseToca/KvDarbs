<?php

namespace App\Forms\Components;

use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Filament\Forms\Set;

class DurationPicker
{
    public static function make(string $name): Fieldset
    {
        return Fieldset::make('Duration')
            ->columns(2)
            ->schema([
                TextInput::make("{$name}_hours")
                    ->label(__('fields.labels.hours'))
                    ->numeric()
                    ->minValue(0)
                    ->reactive()
                    ->formatStateUsing(fn (Get $get) =>
                    intdiv((int) ($get($name) ?? 0), 60)
                    )
                    ->afterStateUpdated(fn (Get $get, Set $set) =>
                        $set(
                            $name,
                            ((int) $get("{$name}_hours") * 60)
                            + (int) $get("{$name}_minutes")
                        )
                    ),
                TextInput::make("{$name}_minutes")
                    ->label(__('fields.labels.minutes'))
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(59)
                    ->reactive()
                    ->formatStateUsing(fn (Get $get) =>
                        ((int) ($get($name) ?? 0)) % 60
                    )
                    ->afterStateUpdated(fn (Get $get, Set $set) =>
                        $set(
                            $name,
                            ((int) $get("{$name}_hours") * 60)
                            + (int) $get("{$name}_minutes")
                        )
                    ),
                Hidden::make($name)
                    ->dehydrated()
                    ->rules(['required', 'integer', 'min:0'])
                    ->required(),
            ]);
    }
}
